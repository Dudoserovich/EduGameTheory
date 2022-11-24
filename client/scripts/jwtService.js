import Cookies from 'universal-cookie';

export function setJWT(JWTToken) {
    const cookies = new Cookies();
    cookies.set('JWT-Token', JWTToken, { path: '/' });
}

export function getJWT() {
    const cookies = new Cookies();
    return cookies.get('JWT-Token');
}

export function removeJWT() {
    const cookies = new Cookies();
    cookies.remove('JWT-Token', { path: '/' });
}

export function setRefreshToken(refreshToken) {
    const cookies = new Cookies();
    cookies.set('refresh_token', refreshToken, { path: '/' });
}

export function getRefreshToken() {
    const cookies = new Cookies();
    return cookies.get('refresh_token');
}

export function removeRefreshToken() {
    const cookies = new Cookies();
    return cookies.remove('refresh_token', { path: '/' });
}