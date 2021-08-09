<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job posting bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

namespace Dreibein\JobpostingBundle\Job;

use Contao\Config;
use Contao\ContentModel;
use Contao\Controller;
use Contao\Date;
use Contao\File;
use Contao\FilesModel;
use Contao\Frontend;
use Contao\FrontendTemplate;
use Contao\Image\PictureConfiguration;
use Contao\LayoutModel;
use Contao\Model;
use Contao\Model\Collection;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;
use Contao\Template;
use Dreibein\JobpostingBundle\Model\JobCategoryModel;
use Dreibein\JobpostingBundle\Model\JobModel;
use Exception;

class JobParser
{
    // private Studio $studio;
    private UrlGenerator $urlGenerator;
    private Model $model;
    private ?PageModel $page;
    private bool $init = false;

    public function __construct(/* Studio $studio, */ UrlGenerator $urlGenerator)
    {
        // $this->studio = $studio;
        $this->urlGenerator = $urlGenerator;
    }

    public function init(Model $model, ?PageModel $page): void
    {
        $this->model = $model;
        $this->page = $page;
        $this->init = true;
    }

    /**
     * Get a list of parsed templates for all jobs.
     *
     * @param JobModel[]|Collection $jobs
     *
     * @throws Exception
     *
     * @return array
     */
    public function parseJobs(Collection $jobs): array
    {
        // Check if the parser was initialized
        if (false === $this->init) {
            throw new Exception('JobParser was not initialized correctly!');
        }

        $limit = $jobs->count();
        if ($limit < 1) {
            return [];
        }

        $count = 0;
        $parsedArticles = [];
        $uuids = [];

        // Loop over all jobs to collect all uuids of the images
        foreach ($jobs as $job) {
            if (true === $job->isAddImage() && '' !== $job->getSingleSRC()) {
                $uuids[] = $job->getSingleSRC();
            }
        }

        // Preload all images in one query, so they are loaded into the model registry
        FilesModel::findMultipleByUuids($uuids);

        foreach ($jobs as $job) {
            // generate parse the templates for all the jobs
            $cssClass = ((1 === ++$count) ? 'first' : '') . (($count === $limit ? ' last' : '')) . ((($count % 2) === 0) ? ' odd' : ' even');
            $parsedArticles[] = $this->parseJob($job, $cssClass, $count);
        }

        return $parsedArticles;
    }

    /**
     * @param JobModel $job
     * @param string   $cssClass
     * @param int      $count
     *
     * @throws \Exception
     *
     * @return string
     */
    public function parseJob(JobModel $job, string $cssClass = '', int $count = 0): string
    {
        // Check if the parser was initialized
        if (false === $this->init) {
            throw new \Exception('JobParser was not initialized correctly!');
        }

        // Initialize the job detail template
        $template = new FrontendTemplate($this->model->job_template ?: 'job_latest');

        // Add all columns of the job to the template
        $template->setData($job->row());

        $categories = StringUtil::deserialize($template->categories, true);
        $categoryData = [];
        foreach ($categories as $categoryId) {
            $category = JobCategoryModel::findById((int) $categoryId);
            if ($category) {
                $categoryData[$category->getId()] = $category;
            }
        }
        $template->categories = $categoryData;

        if ('' !== $job->getCssClass()) {
            $cssClass = ' ' . $job->getCssClass() . $cssClass;
        }

        // Add some data to the template
        $template->class = $cssClass;
        $template->headline = $job->getTitle();
        $template->subHeadline = $job->getSubHeadline();
        $template->hasSubHeadline = ('' !== $job->getSubHeadline());
        $template->linkHeadline = $this->generateLink($job->getTitle(), $job);
        $template->more = $this->generateLink($GLOBALS['TL_LANG']['MSC']['more'], $job, true);
        $template->link = $this->urlGenerator->generateJobUrl($job);
        $template->archive = $job->getArchive();
        $template->count = $count;
        $template->text = '';
        $template->hasText = false;
        $template->hasTeaser = false;

        if ('' !== $job->getTeaser()) {
            $template->hasTeaser = true;
            $template->teaser = StringUtil::encodeEmail(StringUtil::toHtml5($job->getTeaser()));
        }

        $id = $job->getId();
        $template->text = static function () use ($id) {
            $text = '';
            $contentModels = ContentModel::findPublishedByPidAndTable($id, 'tl_job');
            if (null === $contentModels) {
                return $text;
            }

            // Collect all the content elements
            foreach ($contentModels as $contentModel) {
                $text .= Controller::getContentElement($contentModel);
            }

            return $text;
        };

        $template->hasText = static function () use ($id) {
            return ContentModel::countPublishedByPidAndTable($id, 'tl_job');
        };

        $dateimFormat = $this->page ? $this->page->datimFormat : 'd.m.Y';
        $template->date = Date::parse($dateimFormat, $job->getDateTime());
        $template->timestamp = $job->getDateTime();
        $template->datetime = date('Y-m-d\TH:i:sP', $job->getDateTime());
        $template->addImage = false;
        $template->addBefore = false;

        // Add an image
        if ($job->isAddImage()) {
            $objModel = FilesModel::findByUuid($job->getSingleSRC());

            if (null !== $objModel && is_file(System::getContainer()->getParameter('kernel.project_dir') . '/' . $objModel->path)) {
                $arrJob = $job->row();

                $imgSize = $job->getSize();
                if ($imgSize) {
                    $size = StringUtil::deserialize($imgSize);

                    if ($size[0] > 0 || $size[1] > 0 || is_numeric($size[2]) || ($size[2][0] ?? null) === '_') {
                        $arrJob['size'] = $imgSize;
                    }
                }

                $arrJob['singleSRC'] = $objModel->path;
                $this->addImageToTemplate($template, $arrJob, null, null, $objModel);

                if (!$template->fullsize && !$template->imageUrl) {
                    $picture = $template->picture;
                    unset($picture['title']);
                    $template->picture = $picture;

                    $template->href = $template->link;
                    $template->linkTitle = StringUtil::specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['readMore'], $job->getTitle()), true);

                    if ('external' === $template->source && $template->target && false === strpos($template->attributes, 'target="_blank"')) {
                        $template->attributes .= ' target="_blank"';
                    }
                }
            }
        }

        // TODO: Check for parseArticles Hook (like in ModuleNews)

        // TODO: Check caching (like in ModulesNews)

        return $template->parse();
    }

    /**
     * Universal function to generate a specific html link.
     *
     * @param string   $link
     * @param JobModel $job
     * @param bool     $isReadMore
     *
     * @return string
     */
    private function generateLink(string $link, JobModel $job, bool $isReadMore = false): string
    {
        return sprintf(
            '<a href="%s" title="%s" itemprop="url">%s%s</a>',
            $this->urlGenerator->generateJobUrl($job),
            StringUtil::specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['readMore'], $job->getTitle()), true),
            ($isReadMore ? $link : '<span itemprop="headline">' . $link . '</span>'),
            '<span class="invisible"> ' . $job->getTitle() . '</span>'
        );
    }

    /**
     * Create the picture for the template and add the image data to it.
     *
     * @param FrontendTemplate $template
     * @param JobModel         $job
     * @param null             $intMaxWidth
     * @param null             $strLightboxId
     * @param FilesModel|null  $objModel
     * @param mixed            $arrItem
     */
    private function addImageToTemplate(FrontendTemplate $objTemplate, $arrItem, $intMaxWidth = null, $strLightboxId = null, FilesModel $objModel = null): void
    {
        try {
            $objFile = new File($arrItem['singleSRC']);
        } catch (\Exception $e) {
            $objFile = null;
        }

        $imgSize = $objFile->imageSize ?? [];
        $size = StringUtil::deserialize($arrItem['size']);

        if (is_numeric($size)) {
            $size = [0, 0, (int) $size];
        } elseif (!$size instanceof PictureConfiguration) {
            if (!\is_array($size)) {
                // $size = [];
            }

            // $size += [0, 0, 'crop'];
        }

        if (null === $intMaxWidth) {
            $intMaxWidth = Config::get('maxImageWidth');
        }

        $request = System::getContainer()->get('request_stack')->getCurrentRequest();

        if ($request && System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request)) {
            $arrMargin = [];
        } else {
            $arrMargin = StringUtil::deserialize($arrItem['imagemargin']);
        }

        // Store the original dimensions
        $objTemplate->width = $imgSize[0];
        $objTemplate->height = $imgSize[1];

        // Adjust the image size
        if ($intMaxWidth > 0) {
            @trigger_error('Using a maximum front end width has been deprecated and will no longer work in Contao 5.0. Remove the "maxImageWidth" configuration and use responsive images instead.', \E_USER_DEPRECATED);

            // Subtract the margins before deciding whether to resize (see #6018)
            if (\is_array($arrMargin) && 'px' === $arrMargin['unit']) {
                $intMargin = (int) $arrMargin['left'] + (int) $arrMargin['right'];

                // Reset the margin if it exceeds the maximum width (see #7245)
                if ($intMaxWidth - $intMargin < 1) {
                    $arrMargin['left'] = '';
                    $arrMargin['right'] = '';
                } else {
                    $intMaxWidth -= $intMargin;
                }
            }

            if (\is_array($size) && ($size[0] > $intMaxWidth || (!$size[0] && !$size[1] && (!$imgSize[0] || $imgSize[0] > $intMaxWidth)))) {
                // See #2268 (thanks to Thyon)
                $ratio = ($size[0] && $size[1]) ? $size[1] / $size[0] : (($imgSize[0] && $imgSize[1]) ? $imgSize[1] / $imgSize[0] : 0);

                $size[0] = $intMaxWidth;
                $size[1] = floor($intMaxWidth * $ratio);
            }
        }

        $container = System::getContainer();

        try {
            $projectDir = $container->getParameter('kernel.project_dir');
            $staticUrl = $container->get('contao.assets.files_context')->getStaticUrl();
            $picture = $container->get('contao.image.picture_factory')->create($projectDir . '/' . $arrItem['singleSRC'], $size);

            $picture = [
                'img' => $picture->getImg($projectDir, $staticUrl),
                'sources' => $picture->getSources($projectDir, $staticUrl),
            ];

            $src = $picture['img']['src'];

            if ($src !== $arrItem['singleSRC']) {
                $objFile = new File(rawurldecode($src));
            }
        } catch (\Exception $e) {
            System::log('Image "' . $arrItem['singleSRC'] . '" could not be processed: ' . $e->getMessage(), __METHOD__, TL_ERROR);

            $src = '';
            $picture = ['img' => ['src' => '', 'srcset' => ''], 'sources' => []];
        }

        // Image dimensions
        if ($objFile && isset($objFile->imageSize[0], $objFile->imageSize[1])) {
            $objTemplate->arrSize = $objFile->imageSize;
            $objTemplate->imgSize = ' width="' . $objFile->imageSize[0] . '" height="' . $objFile->imageSize[1] . '"';
        }

        $arrMeta = [];

        // Load the meta data
        if ($objModel instanceof FilesModel) {
            if (TL_MODE === 'FE') {
                global $objPage;

                $arrMeta = Frontend::getMetaData($objModel->meta, $objPage->language);

                if (empty($arrMeta) && null !== $objPage->rootFallbackLanguage) {
                    $arrMeta = Frontend::getMetaData($objModel->meta, $objPage->rootFallbackLanguage);
                }
            } else {
                $arrMeta = Frontend::getMetaData($objModel->meta, $GLOBALS['TL_LANGUAGE']);
            }

            Controller::loadDataContainer('tl_files');

            // Add any missing fields
            foreach (array_keys($GLOBALS['TL_DCA']['tl_files']['fields']['meta']['eval']['metaFields']) as $k) {
                if (!isset($arrMeta[$k])) {
                    $arrMeta[$k] = '';
                }
            }

            $arrMeta['imageTitle'] = $arrMeta['title'];
            $arrMeta['imageUrl'] = $arrMeta['link'];
            unset($arrMeta['title'], $arrMeta['link']);

            // Add the meta data to the item
            if (!$arrItem['overwriteMeta']) {
                foreach ($arrMeta as $k => $v) {
                    switch ($k) {
                        case 'alt':
                        case 'imageTitle':
                            $arrItem[$k] = StringUtil::specialchars($v);
                            break;

                        default:
                            $arrItem[$k] = $v;
                            break;
                    }
                }
            }
        }

        $picture['alt'] = StringUtil::specialchars($arrItem['alt']);

        // Move the title to the link tag so it is shown in the lightbox
        if ($arrItem['imageTitle'] && !$arrItem['linkTitle'] && ($arrItem['fullsize'] || $arrItem['imageUrl'])) {
            $arrItem['linkTitle'] = $arrItem['imageTitle'];
            unset($arrItem['imageTitle']);
        }

        if (isset($arrItem['imageTitle'])) {
            $picture['title'] = StringUtil::specialchars($arrItem['imageTitle']);
        }

        $objTemplate->picture = $picture;

        // Provide an ID for single lightbox images in HTML5 (see #3742)
        if (null === $strLightboxId && $arrItem['fullsize'] && $objTemplate instanceof Template && !empty($arrItem['id'])) {
            $strLightboxId = substr(md5($objTemplate->getName() . '_' . $arrItem['id']), 0, 6);
        }

        // Float image
        if ($arrItem['floating']) {
            $objTemplate->floatClass = ' float_' . $arrItem['floating'];
        }

        // Do not override the "href" key (see #6468)
        $strHrefKey = $objTemplate->href ? 'imageHref' : 'href';
        $lightboxSize = StringUtil::deserialize($arrItem['lightboxSize'] ?? null, true);

        if (!$lightboxSize && $arrItem['fullsize'] && isset($GLOBALS['objPage']->layoutId)) {
            $lightboxSize = StringUtil::deserialize(LayoutModel::findByPk($GLOBALS['objPage']->layoutId)->lightboxSize ?? null, true);
        }

        // Image link
        if (TL_MODE === 'FE' && $arrItem['imageUrl']) {
            $objTemplate->$strHrefKey = $arrItem['imageUrl'];
            $objTemplate->attributes = '';

            if ($arrItem['fullsize']) {
                // Always replace insert tags (see #2674)
                $imageUrl = Controller::replaceInsertTags($arrItem['imageUrl']);

                $blnIsExternal = 0 === strncmp($imageUrl, 'http://', 7) || 0 === strncmp($imageUrl, 'https://', 8);

                // Open images in the lightbox
                if (preg_match('/\.(' . strtr(preg_quote(Config::get('validImageTypes'), '/'), ',', '|') . ')$/i', $imageUrl)) {
                    // Do not add the TL_FILES_URL to external URLs (see #4923)
                    if (!$blnIsExternal) {
                        try {
                            $projectDir = $container->getParameter('kernel.project_dir');
                            $staticUrl = $container->get('contao.assets.files_context')->getStaticUrl();

                            // The image url is always an url encoded string and must be decoded beforehand (see #2674)
                            $picture = $container->get('contao.image.picture_factory')->create($projectDir . '/' . urldecode($imageUrl), $lightboxSize);

                            $objTemplate->lightboxPicture = [
                                'img' => $picture->getImg($projectDir, $staticUrl),
                                'sources' => $picture->getSources($projectDir, $staticUrl),
                            ];

                            $objTemplate->$strHrefKey = $objTemplate->lightboxPicture['img']['src'];
                        } catch (\Exception $e) {
                            $objTemplate->$strHrefKey = Controller::addFilesUrlTo($imageUrl);
                            $objTemplate->lightboxPicture = ['img' => ['src' => $objTemplate->$strHrefKey, 'srcset' => $objTemplate->$strHrefKey], 'sources' => []];
                        }
                    }

                    $objTemplate->attributes = ' data-lightbox="' . $strLightboxId . '"';
                } else {
                    $objTemplate->attributes = ' target="_blank"';

                    if ($blnIsExternal) {
                        $objTemplate->attributes .= ' rel="noreferrer noopener"';
                    }
                }
            }
        }

        // Fullsize view
        elseif (TL_MODE === 'FE' && $arrItem['fullsize']) {
            try {
                $projectDir = $container->getParameter('kernel.project_dir');
                $staticUrl = $container->get('contao.assets.files_context')->getStaticUrl();
                $picture = $container->get('contao.image.picture_factory')->create($projectDir . '/' . $arrItem['singleSRC'], $lightboxSize);

                $objTemplate->lightboxPicture = [
                    'img' => $picture->getImg($projectDir, $staticUrl),
                    'sources' => $picture->getSources($projectDir, $staticUrl),
                ];

                $objTemplate->$strHrefKey = $objTemplate->lightboxPicture['img']['src'];
            } catch (\Exception $e) {
                $objTemplate->$strHrefKey = Controller::addFilesUrlTo(System::urlEncode($arrItem['singleSRC']));
                $objTemplate->lightboxPicture = ['img' => ['src' => $objTemplate->$strHrefKey, 'srcset' => $objTemplate->$strHrefKey], 'sources' => []];
            }

            $objTemplate->attributes = ' data-lightbox="' . $strLightboxId . '"';
        }

        // Add the meta data to the template
        foreach (array_keys($arrMeta) as $k) {
            $objTemplate->$k = $arrItem[$k];
        }

        // Do not urlEncode() here because getImage() already does (see #3817)
        $objTemplate->src = Controller::addFilesUrlTo($src);
        $objTemplate->singleSRC = $arrItem['singleSRC'];
        $objTemplate->linkTitle = StringUtil::specialchars($arrItem['linkTitle'] ?: $arrItem['title']);
        $objTemplate->fullsize = $arrItem['fullsize'] ? true : false;
        $objTemplate->addBefore = ('below' !== $arrItem['floating']);
        $objTemplate->margin = Controller::generateMargin($arrMargin);
        $objTemplate->addImage = true;
    }

    /*
    private function addImageToTemplate(JobModel $job, FrontendTemplate $template): void
    {
        // Get the image size from the job itself and override it with settings from the module if given
        $imgSize = $job->getSize() ?: null;
        if ($this->model->imgSize) {
            $size = StringUtil::deserialize($this->model->imgSize);

            if ($size[0] > 0 || $size[1] > 0 || is_numeric($size[2]) || ($size[2][0] ?? null) === '_') {
                $imgSize = $this->model->imgSize;
            }
        }

        // Generate the correct picture data
        $figureBuilder = $this->studio->createFigureBuilder();
        try {
            $figure = $figureBuilder
                ->fromUuid($job->getSingleSRC())
                ->setSize($imgSize)
                ->setMetadata($job->getOverwriteMetadata())
                ->enableLightbox($job->isFullsize())
                ->build()
            ;
        } catch (InvalidResourceException | \LogicException $e) {
            // builder was not able to generate the figure
            return;
        }

        // Rebuild with link to news article if none is set
        if (!$figure->getLinkHref()) {
            $linkTitle = StringUtil::specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['readMore'], $job->getTitle()), true);

            $figure = $figureBuilder
                ->setLinkHref($template->link)
                ->setLinkAttribute('title', $linkTitle)
                ->setOptions(['linkTitle' => $linkTitle]) // Backwards compatibility
                ->build()
            ;
        }

        // Use the legacy way to add the data to the template
        $figure->applyLegacyTemplateData($template, $job->getImagemargin(), $job->getFloating());
    }
    */
}
