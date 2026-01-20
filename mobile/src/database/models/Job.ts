import { Model } from 'sequelize';
import { JobAttributes } from '../../types/job';

class Job extends Model<JobAttributes> implements JobAttributes {
    id!: number;
    title!: string;
    description!: string;
    status!: string;
    createdAt!: Date;
    updatedAt!: Date;

    // Define any additional methods or relationships here
}

export default Job;