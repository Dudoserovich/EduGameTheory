import {Roles} from "./rolesConfig"

export const headerConfig = {
    user: [
        {
            name: 'Пройти компанию',
            href: '/company'
        },
        {
            name: 'Пройти обучение',
            href: '/education'
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
                },
                {
                    name: 'Методы',
                    href: '/materials/methods'
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
            name: 'Конструктор заданий',
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
                },
                {
                    name: 'Методы',
                    href: '/materials/methods'
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
                },
                {
                    name: 'Методы',
                    href: '/materials/methods'
                }
            ],
            href: '/materials'
        },
        {
            name: 'Пользователи',
            href: '/selfAttestations'
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