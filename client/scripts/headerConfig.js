import { Roles } from "./rolesConfig"

export const headerConfig = {
    user: [
        // {
        //     name: 'Пройти компанию',
        //     href: '/company'
        // },
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
                }
            ],
            href: '/materials'
        }
    ],

    admin: [
        {
            name: 'Конструктор заданий',
            href: '/selfAttestations'
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
    if(role === Roles.ADMIN) {
        return headerConfig.admin;
    }

    if(role === Roles.TEACHER) {
        return headerConfig.teacher;
    }

    return headerConfig.user;
}