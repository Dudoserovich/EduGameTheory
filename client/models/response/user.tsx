export interface IUser1 {
    id: number,
    full_name: string
}

export interface IUser extends IUser1 {
    avatar_name: string,
    avatar_base64: string,
    login: string,
    email: string,
    roles: string[]
}