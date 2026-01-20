/**
 * Job Entity
 * Domain model for field service jobs
 */

export interface Job {
  id: number;
  title: string;
  description: string;
  organization_id: number;
  field_id?: number;
  assignee_id?: number;
  status: 'pending' | 'in_progress' | 'completed' | 'cancelled';
  priority: 'low' | 'medium' | 'high';
  due_date?: string;
  started_at?: string;
  completed_at?: string;
  created_at: string;
  updated_at: string;
}

export interface JobCreateData {
  title: string;
  description: string;
  field_id?: number;
  assignee_id?: number;
  status?: 'pending' | 'in_progress' | 'completed' | 'cancelled';
  priority?: 'low' | 'medium' | 'high';
  due_date?: string;
}

export interface JobUpdateData {
  title?: string;
  description?: string;
  status?: 'pending' | 'in_progress' | 'completed' | 'cancelled';
  priority?: 'low' | 'medium' | 'high';
  assignee_id?: number;
  due_date?: string;
}
