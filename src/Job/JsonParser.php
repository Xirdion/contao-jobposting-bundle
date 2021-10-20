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

use Contao\Environment;
use Contao\FilesModel;
use Dreibein\JobpostingBundle\Model\JobModel;

class JsonParser
{
    protected array $json;
    protected JobModel $job;

    /**
     * @param JobModel $job
     *
     * @return string
     */
    public function parseJob(JobModel $job): string
    {
        // Do not generate the json-data if you can not apply for the job
        if (false === $job->isApplyActive()) {
            return '';
        }

        $date = new \DateTimeImmutable();
        $this->job = $job;
        $this->json = [
            '@context' => 'https://schema.org/',
            '@type' => 'JobPosting',
            'identifier' => [
                'name' => $job->getCompany(),
                'value' => $job->id,
            ],
            'title' => $job->getTitle(),
            'description' => htmlspecialchars(strip_tags(nl2br($job->getTeaser()))),
            'datePosted' => ($date->setTimestamp($job->getDate()))->format('Y-m-d'),
        ];

        if ($job->getStop()) {
            $this->json['validThrough'] = ($date->setTimestamp($job->getStop()))->format('Y-m-d');
        }

        // Add the organization of the job to the json array
        $this->addOrganization();

        // Add the job location to the json array
        $this->addLocation();

        // Add the employment types
        $this->addEmploymentType();

        // Add some non-mandatory data to the json
        $this->addSalary();
        $this->addResponsibility();
        $this->addSkills();
        $this->addQualifications();
        $this->addEducationRequirements();
        $this->addExperienceRequirements();

        try {
            return json_encode($this->json, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return '';
        }
    }

    /**
     * Extend the json data with the hiring organization.
     */
    protected function addOrganization(): void
    {
        $json = [
            '@type' => 'Organization',
            'name' => $this->job->getCompany(),
        ];

        if ($this->job->getCompanyUrl()) {
            $json['sameAs'] = $this->job->getCompanyUrl();
        }

        if ($this->job->getCompanyLogo()) {
            $file = FilesModel::findByUuid($this->job->getCompanyLogo());
            if (null !== $file) {
                $json['logo'] = Environment::get('base') . $file->path;
                // TODO: use the symfony request object!
            }
        }

        $this->json['hiringOrganization'] = $json;
    }

    /**
     * Extend the json data with the location(s) of the job.
     */
    protected function addLocation(): void
    {
        // Check if it is a remote job or not
        if ($this->job->isRemote()) {
            $this->json['applicantLocationRequirements'] = [
                '@type' => 'Country',
                'name' => strtoupper($this->job->getCountry()),
            ];
            $this->json['jobLocationType'] = 'TELECOMMUTE';
        }

        // TODO: there could be multiple locations for a job
        $json = [
            '@type' => 'Place',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $this->job->getStreet(),
                'addressLocality' => $this->job->getCity(),
                'addressRegion' => $this->job->getRegion(),
                'postalCode' => $this->job->getPostal(),
                'addressCountry' => strtoupper($this->job->getCountry()),
            ],
        ];

        $this->json['jobLocation'] = $json;
    }

    /**
     * Add the given employment types to the json data.
     */
    protected function addEmploymentType(): void
    {
        if (empty($this->job->getJobType())) {
            return;
        }

        $this->json['employmentType'] = $this->job->getJobType();
    }

    /**
     * Add the base salary to the json data.
     */
    protected function addSalary(): void
    {
        if (!$this->job->getSalary()) {
            return;
        }

        $json = [
            '@type' => 'MonetaryAmount',
            'currency' => 'EUR',
            'value' => [
                '@type' => 'QuantitativeValue',
                'value' => $this->job->getSalary(),
                'unitText' => $this->job->getSalaryInterval(),
            ],
        ];

        $this->json['baseSalary'] = $json;
    }

    /**
     * Add the expected responsibilities to the json data.
     */
    protected function addResponsibility(): void
    {
        if (!$this->job->getResponsibility()) {
            return;
        }

        $this->json['responsibilities'] = $this->job->getResponsibility();
    }

    /**
     * Add the needed skills to the json data.
     */
    protected function addSkills(): void
    {
        if (!$this->job->getSkills()) {
            return;
        }

        $this->json['skills'] = $this->job->getSkills();
    }

    /**
     * Add the needed qualifications to the json data.
     */
    protected function addQualifications(): void
    {
        if (!$this->job->getQualification()) {
            return;
        }

        $this->json['qualifications'] = $this->job->getQualification();
    }

    /**
     * Add the needed education level to the json data.
     */
    protected function addEducationRequirements(): void
    {
        if (!$this->job->getEducation()) {
            return;
        }

        $this->json['educationRequirements'] = $this->job->getEducation();
    }

    /**
     * Add the needed job experience to the json data.
     */
    protected function addExperienceRequirements(): void
    {
        if (!$this->job->getExperience()) {
            return;
        }

        $this->json['experienceRequirements'] = $this->job->getExperience();
    }
}
