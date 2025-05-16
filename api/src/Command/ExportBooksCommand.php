<?php

namespace App\Command;

use App\Repository\BookRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class ExportBooksCommand extends Command
{
    protected static $defaultName = 'app:export-books';
    
    protected static $defaultDescription = 'Export books to JSON file';

    private BookRepository $bookRepository;  

    public function __construct(BookRepository $bookRepository)
    {
        parent::__construct('app:export-books');
        $this->bookRepository = $bookRepository;
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'output-dir',
                'd',
                InputOption::VALUE_OPTIONAL,
                'Directory to save the JSON file',
                'var/export'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $outputDir = rtrim($input->getOption('output-dir'), '/');
        $filesystem = new Filesystem();

        if (!$filesystem->exists($outputDir)) {
            $filesystem->mkdir($outputDir, 0755);
            $output->writeln("Created directory: $outputDir");
        }

        $books = $this->bookRepository->findAll();

        $data = [];

        foreach ($books as $book) {
            // Get categories names
            $categories = [];
            foreach ($book->getCategories() as $category) {
                $categories[] = $category->getName();
            }

            // Count reviews
            $reviewsCount = $book->getReviews()->count();

            // Count bookmarks
            $bookmarksCount = $book->getBookmarks()->count();

            // Find active users who have both review and bookmark
            $reviewUsers = [];
            foreach ($book->getReviews() as $review) {
                $reviewUsers[$review->getUser()->getUserIdentifier()] = true;
            }
            $bookmarkUsers = [];
            foreach ($book->getBookmarks() as $bookmark) {
                $bookmarkUsers[$bookmark->getUser()->getUserIdentifier()] = true;
            }

            $activeUsers = array_values(array_intersect_key($reviewUsers, $bookmarkUsers));

            $data[] = [
                'id' => $book->getId()->toRfc4122(),
                // 'author' => $book->getAuthor(),
                // 'title' => $book->getTitle(),
                'categories' => $categories,
                'reviews' => $reviewsCount,
                'bookmarks' => $bookmarksCount,
                'activeUsers' => $activeUsers,
            ];
        }

        $filePath = $outputDir . '/books.json';
        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));

        $output->writeln("Exported " . count($books) . " books to $filePath");

        return Command::SUCCESS;
    }
}
