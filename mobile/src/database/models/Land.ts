import { Model } from 'sequelize';

export interface LandAttributes {
    id: number;
    organizationId: number;
    name: string;
    area: number; // in acres
    coordinates: string; // GeoJSON or similar format
    createdAt?: Date;
    updatedAt?: Date;
}

export class Land extends Model<LandAttributes> implements LandAttributes {
    public id!: number;
    public organizationId!: number;
    public name!: string;
    public area!: number;
    public coordinates!: string;
    public createdAt!: Date;
    public updatedAt!: Date;

    // Additional methods can be defined here
}