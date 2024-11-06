<?php

namespace App\State;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Company;
use App\Repository\CompanyRepository;

class CompanyProcessor implements ProcessorInterface
{
    public function __construct(
        private PersistProcessor  $persistProcessor,
        private CompanyRepository $companyRepository
    )
    {
        //
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!$data instanceof Company) {
            throw new \InvalidArgumentException('Data must be an instance of Company');
        }
        $existingCompany = $this->companyRepository->findOneBy(['name' => $data->getName()]);

        if ($existingCompany) {
            throw new \RuntimeException('A company with this name already exists.');
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
