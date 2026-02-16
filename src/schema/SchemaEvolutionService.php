<?php

namespace struktal\ORM\schema;

class SchemaEvolutionService {
    /**
     * Execute all schema evolutions in the given directory that have not been executed yet
     * @param string $schemaEvolutionsDirectory
     * @return void
     */
    public static function evolve(string $schemaEvolutionsDirectory): void {
        self::createSchemaEvolutionTable();

        if(!is_dir($schemaEvolutionsDirectory)) {
            trigger_error("Schema evolutions directory does not exist: " . $schemaEvolutionsDirectory, E_USER_WARNING);
            return;
        }

        $files = scandir($schemaEvolutionsDirectory);
        foreach($files as $file) {
            if(!str_ends_with($file, ".sql")) {
                continue;
            }

            self::executeEvolution($schemaEvolutionsDirectory, $file);
        }
    }

    /**
     * Execute a single schema evolution if it has not been executed yet
     * @param string $schemaEvolutionsDirectory
     * @param string $evolutionFile
     * @return void
     */
    private static function executeEvolution(string $schemaEvolutionsDirectory, string $evolutionFile): void {
        if(self::isExecuted($evolutionFile)) {
            return;
        }

        $struktalEvolutionFile = $schemaEvolutionsDirectory . DIRECTORY_SEPARATOR . $evolutionFile; // Prefixed variable to avoid conflicts with other variable spaces

        if(str_ends_with($evolutionFile, ".sql")) {
            $sql = file_get_contents($struktalEvolutionFile);
            if(!$sql) {
                trigger_error("Failed to read SQL evolution file: " . $struktalEvolutionFile, E_USER_WARNING);
                return;
            }

            $connection = \struktal\ORM\Database\Database::getConnection();
            if(!$connection instanceof \PDO) {
                trigger_error("Database connection is not an instance of PDO", E_USER_WARNING);
                return;
            }

            $connection->beginTransaction();
            try {
                $connection->exec($sql);
                $connection->commit();
            } catch(\Exception $e) {
                $connection->rollBack();
                throw $e;
            }
        } else {
            // Other file types can be implemented here, e.g. PHP files, which can contain more complex evolutions
            trigger_error("Unsupported schema evolution file type: " . $evolutionFile, E_USER_WARNING);
        }

        $evolution = new SchemaEvolution();
        $evolution->evolution = $evolutionFile;
        $evolution->executed = new \DateTimeImmutable();
        SchemaEvolution::dao()->save($evolution);
    }

    /**
     * Check whether a schema evolution has already been executed
     * @param string $evolutionFile
     * @return bool
     */
    private static function isExecuted(string $evolutionFile): bool {
        $evolution = SchemaEvolution::dao()->getObject([
            "evolution" => $evolutionFile,
            new \struktal\ORM\DAOFilter(
                \struktal\ORM\DAOFilterOperator::NOT_EQUALS,
                "executed",
                null
            )
        ]);

        return $evolution instanceof SchemaEvolution;
    }

    /**
     * Create the table for schema evolutions if it does not exist yet
     * @return void
     */
    private static function createSchemaEvolutionTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS `struktal\ORM\schema\SchemaEvolution` (
                    `id` INT NOT NULL AUTO_INCREMENT,
                    `evolution` VARCHAR(256) NOT NULL UNIQUE,
                    `executed` DATETIME(3) NULL,
                    `created` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
                    `updated` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),
                    PRIMARY KEY (`id`),
                    UNIQUE KEY (`evolution`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        \struktal\ORM\Database\Database::getConnection()->exec($sql);
    }
}

