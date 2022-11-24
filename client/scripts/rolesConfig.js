export const Roles = {
    ADMIN: {
        label: 'Админ',
        value: 'ROLE_ADMIN'
    },
    TEACHER: {
        label: 'Преподаватель',
        value: 'ROLE_TEACHER'
    },
    USER: {
        label: 'Ученик',
        value: 'ROLE_USER'
    }
}

export function getRolesArray() {
    let result = [];
    for (let key in Roles) {
        result.push(Roles[key]);
    }

    return result;
}

export function getUserRole(rolesArr) {
    if (rolesArr.includes(Roles.ADMIN.value)) {
        return Roles.ADMIN;
    }

    if (rolesArr.includes(Roles.TEACHER.value)) {
        return Roles.TEACHER;
    }

    return Roles.USER;
}