import { Migration } from 'sequelize-typescript';

export class CreateJobsTable extends Migration {
  public async up(queryInterface: any): Promise<void> {
    await queryInterface.createTable('jobs', {
      id: {
        type: 'INTEGER',
        autoIncrement: true,
        primaryKey: true,
      },
      title: {
        type: 'STRING',
        allowNull: false,
      },
      description: {
        type: 'TEXT',
        allowNull: true,
      },
      status: {
        type: 'STRING',
        allowNull: false,
        defaultValue: 'pending',
      },
      driverId: {
        type: 'INTEGER',
        allowNull: false,
        references: {
          model: 'drivers',
          key: 'id',
        },
        onUpdate: 'CASCADE',
        onDelete: 'SET NULL',
      },
      landId: {
        type: 'INTEGER',
        allowNull: false,
        references: {
          model: 'lands',
          key: 'id',
        },
        onUpdate: 'CASCADE',
        onDelete: 'SET NULL',
      },
      createdAt: {
        type: 'TIMESTAMP',
        allowNull: false,
        defaultValue: new Date(),
      },
      updatedAt: {
        type: 'TIMESTAMP',
        allowNull: false,
        defaultValue: new Date(),
      },
    });
  }

  public async down(queryInterface: any): Promise<void> {
    await queryInterface.dropTable('jobs');
  }
}