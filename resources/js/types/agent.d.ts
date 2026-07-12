export type AgentTaskStatus = 'pending' | 'in_progress' | 'review' | 'completed' | 'cancelled';

export type AgentDoc = {
    id: number;
    team_id: number;
    title: string;
    slug: string;
    content: string | null;
    version: string;
    metadata: Record<string, unknown> | null;
    created_at: string;
    updated_at: string;
};

export type AgentTask = {
    id: number;
    team_id: number;
    assigned_to: number | null;
    title: string;
    description: string | null;
    status: AgentTaskStatus;
    due_at: string | null;
    completed_at: string | null;
    metadata: Record<string, unknown> | null;
    created_at: string;
    updated_at: string;
    assignee?: {
        id: number;
        name: string;
        email: string;
    } | null;
};

export type TaskDocLink = {
    id: number;
    agent_task_id: number;
    agent_doc_id: number;
};

export type TaskStatusOption = {
    value: AgentTaskStatus;
    label: string;
    color: string;
};