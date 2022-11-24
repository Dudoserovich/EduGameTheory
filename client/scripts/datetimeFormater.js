import { getTrueWordForm } from "./trueWordForm";

const MS_IN_SEC = 1000;
const SEC_IN_MINUTE = 60;
const MINUTE_IN_HOUR = 60;
const HOUR_IN_DAY = 24;
const DAY_IN_WEEK = 7;
const WEEK_IN_MONTH = 4;
const MONTH_IN_YEAR = 12;
const MONTHS = [
    'Января',
    'Февраля',
    'Марта',
    'Апреля',
    'Мая',
    'Июня',
    'Июля',
    'Августа',
    'Сентября',
    'Октября',
    'Ноября',
    'Декабря',
];

export function getFormattedDateTime(dateTime) {
    let dateObj = new Date(Date.parse(dateTime));
    let hours = dateObj.getHours() < 10 ? '0' + dateObj.getHours() : dateObj.getHours();
    let minutes = dateObj.getMinutes() < 10 ? '0' + dateObj.getMinutes() : dateObj.getMinutes();
    return `${dateObj.getDate()} ${MONTHS[dateObj.getMonth()]} ${dateObj.getFullYear()} ${hours}:${minutes}`;
}

export function getTimeAgo(pasteDateTime) {
    let passedTime = (Date.now() - Date.parse(pasteDateTime)) / MS_IN_SEC;

    if (passedTime < 60) {
        if (passedTime < 10) {
            return `только что`;
        }
        let roundedSec = parseInt(passedTime, 10);
        return `${roundedSec} ${getTrueWordForm(roundedSec, ['секунду', 'секунды', 'секунд'])} назад`;
    }

    let minutes = passedTime / SEC_IN_MINUTE;
    if (minutes < MINUTE_IN_HOUR) {
        let roundedMin = parseInt(minutes, 10);
        return `${roundedMin} ${getTrueWordForm(roundedMin, ['минуту', 'минуты', 'минут'])} назад`;
    }
        
    let hours = minutes / MINUTE_IN_HOUR;
    if (hours < HOUR_IN_DAY) {
        let roundedhours = parseInt(hours, 10);
        return `${roundedhours} ${getTrueWordForm(roundedhours, ['час', 'часа', 'часов'])} назад`;
    }

    let days = hours / HOUR_IN_DAY;
    if (days < DAY_IN_WEEK) {
        let roundedDays = parseInt(days, 10);
        return `${roundedDays} ${getTrueWordForm(roundedDays, ['день', 'дня', 'дней'])} назад`;
    }

    let weeks = days / DAY_IN_WEEK;
    if (weeks < WEEK_IN_MONTH) {
        let roundedWeeks = parseInt(weeks, 10);
        return `${roundedWeeks} ${getTrueWordForm(roundedWeeks, ['неделю', 'недели', 'недель'])} назад`;
    }

    let months = weeks / WEEK_IN_MONTH;
    if (months < MONTH_IN_YEAR) {
        let roundedMonth = parseInt(months, 10);
        return `${roundedMonth} ${getTrueWordForm(roundedMonth, ['месяц', 'месяца', 'месяцев'])} назад`;
    }

    let dateTime = new Date(Date.parse(pasteDateTime));
    return `${dateTime.getDate()} ${MONTHS[dateTime.getMonth()]} ${dateTime.getFullYear()}`;
}