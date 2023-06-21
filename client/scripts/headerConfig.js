import {Roles} from "./rolesConfig"

export const headerConfig = {
    user: [
        {
            name: 'Задания',
            href: '/tasks/allTasks'
        },
        {
            name: 'Дополнительные материалы',
            submenus: [
                {
                    name: 'Глосарий терминов',
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
            name: 'Задания',
            href: '/tasks'
        },
    ],

    teacher: [
        {
            name: 'Задания',
            submenus: [
                {
                    name: 'Задания',
                    href: '/tasks/allTasks'
                },
                {
                    name: 'Конструктор заданий',
                    href: '/tasks/creatTask'
                },
                {
                    name: 'Мои задания',
                    href: '/tasks/myTasks'
                },

            ],
            href: '/tasks'
        },
        {
            name: 'Дополнительные материалы',
            submenus: [
                {
                    name: 'Глосарий терминов',
                    href: '/materials/terms'
                },
                {
                    name: 'Литература',
                    href: '/materials/literature'
                }
            ],
            href: '/materials'
        }
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
                {
                    name: 'Мои задания',
                    href: '/tasks/myTasks'
                },

            ],
            href: '/tasks'
        },
        {
            name: 'Дополнительные материалы',
            submenus: [
                {
                    name: 'Глосарий терминов',
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