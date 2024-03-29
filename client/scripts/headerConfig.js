import {Roles} from "./rolesConfig"

export const headerConfig = {
    user: [
        {
            name: 'Задания',
            href: '/tasks/all'
        },
        {
            name: 'Дополнительные материалы',
            submenus: [
                {
                    name: 'Глоссарий терминов',
                    href: '/materials/terms'
                },
                {
                    name: 'Литература',
                    href: '/materials/literature'
                }
            ],
            href: '/materials'
        },
        {
            name: 'Обучение',
            href: '/educations'
        },
        {
            name: 'Пользователи',
            submenus: [
                {
                    name: 'Пользователи',
                    href: '/users/allUsers'
                },
                {
                    name: 'Таблица лидеров',
                    href: '/users/leaders'
                },

            ],
        },
    ],

    teacher: [
        {
            name: 'Дополнительные материалы',
            submenus: [
                {
                    name: 'Глоссарий терминов',
                    href: '/materials/terms'
                },
                {
                    name: 'Литература',
                    href: '/materials/literature'
                }
            ],
            href: '/materials'
        },
        {
            name: 'Пользователи',
            submenus: [
                {
                    name: 'Пользователи',
                    href: '/users/allUsers'
                },
                {
                    name: 'Таблица лидеров',
                    href: '/users/leaders'
                },

            ],
        },
    ],

    admin: [
        {
            name: 'Задания',
            submenus: [
                {
                    name: 'Задания',
                    href: '/tasks/all'
                },
                {
                    name: 'Конструктор заданий',
                    href: '/tasks/createTask'
                },
            ],
            href: '/tasks'
        },
        {
            name: 'Дополнительные материалы',
            submenus: [
                {
                    name: 'Глоссарий терминов',
                    href: '/materials/terms'
                },
                {
                    name: 'Литература',
                    href: '/materials/literature'
                }
            ],
            href: '/materials'
        },
        {
            name: 'Обучение',
            href: '/educations'
        },
        {
            name: 'Пользователи',
            submenus: [
                {
                    name: 'Пользователи',
                    href: '/users/allUsers'
                },
                {
                    name: 'Таблица лидеров',
                    href: '/users/leaders'
                },

            ],
            href: '/users'
        },
    ]
}

export function getHeaderConfigByRole(role) {
    if (role === Roles.ADMIN) {
        return headerConfig.admin;
    }

    if (role === Roles.TEACHER) {
        return headerConfig.teacher;
    }

    return headerConfig.user;
}