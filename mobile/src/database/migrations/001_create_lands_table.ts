import { Migration } from 'sequelize-typescript';

export class CreateLandsTable extends Migration {
  public async up(queryInterface: any): Promise<void> {
    await queryInterface.createTable('lands', {
      id: {
        type: 'INTEGER',
        autoIncrement: true,
        primaryKey: true,
      },
      organizationId: {
        type: 'INTEGER',
        allowNull: false,
        references: {
          model: 'organizations',
          key: 'id',
        },
      },
      name: {
        type: 'STRING',
        allowNull: false,
      },
      area: {
        type: 'FLOAT',
        allowNull: false,
      },
      coordinates: {
        type: 'TEXT',
        allowNull: false,
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
    await queryInterface.dropTable('lands');
  }
}