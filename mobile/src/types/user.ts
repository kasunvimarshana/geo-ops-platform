export interface User {
    id: number;
    name: string;
    email: string;
    role: 'Admin' | 'Owner' | 'Driver' | 'Broker' | 'Accountant';
    organizationId: number;
    createdAt: string;
    updatedAt: string;
}