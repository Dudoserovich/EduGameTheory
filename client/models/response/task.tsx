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
    topic: {
        id: number,
        name: string
    },
    owner: { id: number, fio: string,},
    matrix: number[][],
    flagMatrix: string,
    name_first_player: string,
    name_second_player: string,
    name_first_strategies: string[],
    name_second_strategies: string[],
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