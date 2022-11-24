export interface IUser1 {
    id: number,
    full_name: string
}

export interface IUser extends IUser1 {
    login: string,
    email: string,
    roles: string[]
}