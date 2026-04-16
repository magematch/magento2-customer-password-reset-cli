<?php

declare(strict_types=1);

namespace Rameera\PasswordReset\Console\Command;

use Rameera\Email\Model\Customer\EmailNotification;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\AccountManagement;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;
use Rameera\PasswordReset\Logger\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class Reset extends Command
{
    private const EMAIL = 'email';
    private const CUSTOMER_ID = 'customer_id';

    public function __construct(
        private readonly EmailNotification $emailNotification,
        private readonly Logger $logger,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly AccountManagement $accountManagement,
        private readonly Random $mathRandom,
        private readonly State $appState,
        private readonly FilterBuilder $filterBuilder,
        private readonly SortOrderBuilder $sortOrderBuilder
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('password:reset')
            ->setDescription('Send password reset email(s)')
            ->addOption(self::EMAIL, null, InputOption::VALUE_REQUIRED, 'Customer email')
            ->addOption(self::CUSTOMER_ID, null, InputOption::VALUE_REQUIRED, 'Customer ID greater than or equal to this value');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->setAreaCodeSafe();

        $email = $input->getOption(self::EMAIL);
        $customerId = $input->getOption(self::CUSTOMER_ID);

        if (is_string($email) && $email !== '') {
            $customers = $this->getCustomersByEmail($email);
            $output->writeln('Password reset email trigger for: ' . $email);
        } elseif (is_numeric($customerId)) {
            $customers = $this->getCustomersByMinId((int) $customerId);
            $output->writeln('Password reset email trigger for customers with ID >= ' . (int) $customerId);
        } else {
            $customers = $this->getAllCustomers();
            $output->writeln('Password reset email trigger for all customers');
        }

        $count = count($customers);
        if ($count === 0) {
            $output->writeln('No customers matched criteria.');
            return Command::SUCCESS;
        }

        $this->logger->info('Customer count: ' . $count);
        $this->sendResetPasswordEmails($customers);

        return Command::SUCCESS;
    }

    /**
     * @return CustomerInterface[]
     */
    private function getCustomersByMinId(int $customerId): array
    {
        try {
            $filter = $this->filterBuilder
                ->setField('entity_id')
                ->setConditionType('gteq')
                ->setValue($customerId)
                ->create();

            $sortOrder = $this->sortOrderBuilder
                ->setField('entity_id')
                ->setDirection(SortOrder::SORT_ASC)
                ->create();

            $searchCriteria = (clone $this->searchCriteriaBuilder)
                ->addFilters([$filter])
                ->setSortOrders([$sortOrder])
                ->create();

            $customers = $this->customerRepository->getList($searchCriteria);
            return $customers->getTotalCount() > 0 ? $customers->getItems() : [];
        } catch (Throwable $exception) {
            $this->logger->critical($exception->getMessage());
            return [];
        }
    }

    /**
     * @return CustomerInterface[]
     */
    private function getCustomersByEmail(string $email): array
    {
        try {
            $searchCriteria = (clone $this->searchCriteriaBuilder)
                ->addFilter('email', $email)
                ->create();

            $customers = $this->customerRepository->getList($searchCriteria);
            return $customers->getTotalCount() > 0 ? $customers->getItems() : [];
        } catch (Throwable $exception) {
            $this->logger->critical($exception->getMessage());
            return [];
        }
    }

    /**
     * @return CustomerInterface[]
     */
    private function getAllCustomers(): array
    {
        try {
            $searchCriteria = (clone $this->searchCriteriaBuilder)->create();
            $customers = $this->customerRepository->getList($searchCriteria);
            return $customers->getTotalCount() > 0 ? $customers->getItems() : [];
        } catch (Throwable $exception) {
            $this->logger->critical($exception->getMessage());
            return [];
        }
    }

    /**
     * @param CustomerInterface[] $customers
     */
    private function sendResetPasswordEmails(array $customers): void
    {
        foreach ($customers as $customer) {
            try {
                $newPasswordToken = $this->mathRandom->getUniqueHash();
                $this->accountManagement->changeResetPasswordLinkToken($customer, $newPasswordToken);
                $this->emailNotification->sendEmailRevampedWebsitePassword($customer, $newPasswordToken);
                $this->logger->info($customer->getId() . ' :: Customer Email Sent :: ' . $customer->getEmail());
            } catch (Throwable $exception) {
                $this->logger->error($exception->getMessage());
            }
        }
    }

    private function setAreaCodeSafe(): void
    {
        try {
            $this->appState->setAreaCode(Area::AREA_CRONTAB);
        } catch (LocalizedException) {
        }
    }
}
