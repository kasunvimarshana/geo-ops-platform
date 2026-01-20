import { Migration } from 'sequelize-typescript';

export class CreateSyncQueueTable extends Migration {
  public async up(queryInterface: any): Promise<void> {
    await queryInterface.createTable('sync_queue', {
      id: {
        type: 'INTEGER',
        autoIncrement: true,
        primaryKey: true,
      },
      data: {
        type: 'TEXT',
        allowNull: false,
      },
      createdAt: {
        type: 'DATETIME',
        allowNull: false,
        defaultValue: new Date(),
      },
      updatedAt: {
        type: 'DATETIME',
        allowNull: false,
        defaultValue: new Date(),
      },
    });
  }

  public async down(queryInterface: any): Promise<void> {
    await queryInterface.dropTable('sync_queue');
  }
}