<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250516112731 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Add slug column as nullable
        $this->addSql('ALTER TABLE book ADD slug VARCHAR(255) DEFAULT NULL');

        // Generate slugs for existing records
        $this->addSql("UPDATE book SET slug = CONCAT('book-', id)");

        // Set slug column to NOT NULL
        $this->addSql("ALTER TABLE book ALTER COLUMN slug SET NOT NULL");

        // Create unique index on slug
        $this->addSql("CREATE UNIQUE INDEX UNIQ_BOOK_SLUG ON book (slug)");

        // Add promotion_status column with default value
        $this->addSql("ALTER TABLE book ADD promotion_status VARCHAR(255) DEFAULT 'None' NOT NULL");

        // Update promotion status based on is_promoted
        // $this->addSql("UPDATE book SET promotion_status = 'Basic' WHERE is_promoted = true");
        // $this->addSql("UPDATE book SET promotion_status = 'None' WHERE is_promoted = false");

        // Drop deprecated column
        // $this->addSql("ALTER TABLE book DROP is_promoted");

        //UUID & type/comment changes
        $this->addSql("ALTER TABLE book ALTER id TYPE UUID");
        $this->addSql("COMMENT ON COLUMN book.id IS ''");

        $this->addSql("ALTER TABLE bookmark ALTER id TYPE UUID");
        $this->addSql("ALTER TABLE bookmark ALTER user_id TYPE UUID");
        $this->addSql("ALTER TABLE bookmark ALTER book_id TYPE UUID");
        $this->addSql("ALTER TABLE bookmark ALTER bookmarked_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE");
        $this->addSql("COMMENT ON COLUMN bookmark.id IS ''");
        $this->addSql("COMMENT ON COLUMN bookmark.user_id IS ''");
        $this->addSql("COMMENT ON COLUMN bookmark.book_id IS ''");
        $this->addSql("COMMENT ON COLUMN bookmark.bookmarked_at IS ''");

        $this->addSql("ALTER TABLE parchment ALTER id TYPE UUID");
        $this->addSql("COMMENT ON COLUMN parchment.id IS ''");

        $this->addSql("ALTER TABLE review ALTER id TYPE UUID");
        $this->addSql("ALTER TABLE review ALTER user_id TYPE UUID");
        $this->addSql("ALTER TABLE review ALTER book_id TYPE UUID");
        $this->addSql("ALTER TABLE review ALTER published_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE");
        $this->addSql("COMMENT ON COLUMN review.id IS ''");
        $this->addSql("COMMENT ON COLUMN review.user_id IS ''");
        $this->addSql("COMMENT ON COLUMN review.book_id IS ''");
        $this->addSql("COMMENT ON COLUMN review.published_at IS ''");

        $this->addSql("ALTER TABLE \"user\" ALTER id TYPE UUID");
        $this->addSql("COMMENT ON COLUMN \"user\".id IS ''");
    }


    public function down(Schema $schema): void
    {
        // Re-add the is_promoted column (assuming it was a boolean, default false)
        $this->addSql("ALTER TABLE book ADD is_promoted BOOLEAN DEFAULT FALSE NOT NULL");

        // Restore values from promotion_status to is_promoted
        $this->addSql("UPDATE book SET is_promoted = true WHERE promotion_status = 'Basic' OR promotion_status = 'Pro'");
        $this->addSql("UPDATE book SET is_promoted = false WHERE promotion_status = 'None'");

        // Remove the new promotion_status and slug columns
        $this->addSql("ALTER TABLE book DROP promotion_status");
        $this->addSql("ALTER TABLE book DROP slug");

        // Drop the unique index on slug
        $this->addSql("DROP INDEX UNIQ_BOOK_SLUG");

        // Reverse UUID type changes and column comments
        $this->addSql("ALTER TABLE book ALTER id TYPE UUID USING id::UUID");
        $this->addSql("COMMENT ON COLUMN book.id IS NULL");

        $this->addSql("ALTER TABLE bookmark ALTER id TYPE UUID USING id::UUID");
        $this->addSql("ALTER TABLE bookmark ALTER user_id TYPE UUID USING user_id::UUID");
        $this->addSql("ALTER TABLE bookmark ALTER book_id TYPE UUID USING book_id::UUID");
        $this->addSql("ALTER TABLE bookmark ALTER bookmarked_at TYPE TIMESTAMP WITHOUT TIME ZONE");
        $this->addSql("COMMENT ON COLUMN bookmark.id IS NULL");
        $this->addSql("COMMENT ON COLUMN bookmark.user_id IS NULL");
        $this->addSql("COMMENT ON COLUMN bookmark.book_id IS NULL");
        $this->addSql("COMMENT ON COLUMN bookmark.bookmarked_at IS NULL");

        $this->addSql("ALTER TABLE parchment ALTER id TYPE UUID USING id::UUID");
        $this->addSql("COMMENT ON COLUMN parchment.id IS NULL");

        $this->addSql("ALTER TABLE review ALTER id TYPE UUID USING id::UUID");
        $this->addSql("ALTER TABLE review ALTER user_id TYPE UUID USING user_id::UUID");
        $this->addSql("ALTER TABLE review ALTER book_id TYPE UUID USING book_id::UUID");
        $this->addSql("ALTER TABLE review ALTER published_at TYPE TIMESTAMP WITHOUT TIME ZONE");
        $this->addSql("COMMENT ON COLUMN review.id IS NULL");
        $this->addSql("COMMENT ON COLUMN review.user_id IS NULL");
        $this->addSql("COMMENT ON COLUMN review.book_id IS NULL");
        $this->addSql("COMMENT ON COLUMN review.published_at IS NULL");

        $this->addSql("ALTER TABLE \"user\" ALTER id TYPE UUID USING id::UUID");
        $this->addSql("COMMENT ON COLUMN \"user\".id IS NULL");
    }

}
