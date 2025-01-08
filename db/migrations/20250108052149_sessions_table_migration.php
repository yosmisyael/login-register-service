<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class SessionsTableMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table('sessions', [
            'id' => false,
            'primary_key' => ['id'],
        ]);

        $table->addColumn('id', 'string', [
            'null' => false,
        ])
            ->addColumn('user_id', 'string', [
                'null' => false,
                'limit' => 36,
            ])
            ->addForeignKey('user_id', 'users', 'id', [
                'constraint' => 'fk_users.id_sessions_user_id',
                'delete' => 'CASCADE',
                'update' => 'CASCADE'
            ])
            ->create();
    }
}
