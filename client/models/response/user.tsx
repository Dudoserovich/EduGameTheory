export interface IUser1 {
    id: number,
    full_name: string
}

export interface IUser extends IUser1 {
    avatar: string,
    login: string,
    email: string,
    roles: string[]
}