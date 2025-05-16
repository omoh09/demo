<?php

namespace App\Command;

use App\Repository\ReviewRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ReviewStatsCommand extends Command
{
    protected static $defaultName = 'app:review:peak-period';

    public function __construct(
        private ReviewRepository $reviewRepository
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Displays the day or month with the highest number of reviews published.')
            ->addOption(
                'month',
                null,
                InputOption::VALUE_NONE,
                'If set, display the month (Y-m) instead of the day (Y-m-d)'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $byMonth = $input->getOption('month');

        $period = $this->reviewRepository->findDayOrMonthWithMostReviews($byMonth);

        if ($period === null) {
            $output->writeln('<comment>No reviews found.</comment>');
            return Command::SUCCESS;
        }

        $label = $byMonth ? 'Month' : 'Day';
        $output->writeln(sprintf('%s with the highest number of reviews: %s', $label, $period));

        return Command::SUCCESS;
    }
}
