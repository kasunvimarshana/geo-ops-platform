export interface Job {
    id: number;
    title: string;
    description: string;
    status: 'pending' | 'in_progress' | 'completed';
    assignedTo: number; // Driver ID
    landId: number; // Land ID
    createdAt: string; // ISO date string
    updatedAt: string; // ISO date string
    completedAt?: string; // ISO date string, optional
}