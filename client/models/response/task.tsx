export interface Task {
    id: number,
    name: string,
    description: string,
    matrix: number[],
    flag_matrix: string,
    topic_id: number,
}

export interface Tasks {
    id: number,
    name: string,
    description: string,
    type: string,
    topic: string,
    owner: { id: number, fio: string,},
    matrix: number[][],
    flagMatrix: string,
}
export interface AllTasks {
    allTasks: Tasks[],
}

export interface TasksPlay {
    matrix: number[][],
    chance_first: number,
    chance_second: number,
    description: string,
}