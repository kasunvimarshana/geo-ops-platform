import { Model } from 'sequelize';
import { DataTypes } from 'sequelize';
import { sequelize } from '../sqlite';

class SyncQueue extends Model {
  public id!: number;
  public data!: string;
  public status!: 'pending' | 'completed' | 'failed';
  public createdAt!: Date;
  public updatedAt!: Date;
}

SyncQueue.init(
  {
    id: {
      type: DataTypes.INTEGER,
      autoIncrement: true,
      primaryKey: true,
    },
    data: {
      type: DataTypes.TEXT,
      allowNull: false,
    },
    status: {
      type: DataTypes.ENUM('pending', 'completed', 'failed'),
      allowNull: false,
      defaultValue: 'pending',
    },
    createdAt: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: DataTypes.NOW,
    },
    updatedAt: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: DataTypes.NOW,
    },
  },
  {
    sequelize,
    modelName: 'SyncQueue',
    tableName: 'sync_queue',
    timestamps: true,
  }
);

export default SyncQueue;