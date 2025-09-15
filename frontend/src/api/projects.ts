import {http} from './http'

export type ProjectStatus = 'active' | 'paused' | 'done'

export interface Project {
    id: number
    name: string
    status: ProjectStatus
    created_at: string
}

export async function listProjects(): Promise<Project[]> {
    const { data } = await http.get<Project[]>('/api/projects')
    return data
}

export async function createProject(payload: {name: string, status: ProjectStatus}) {
    const { data } = await http.post<Project>('/api/projects', payload)
    return data; 
}