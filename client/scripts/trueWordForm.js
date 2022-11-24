export function getTrueWordForm(number, forms) {
    return (
        number !== 11 && number % 10 === 1
        ?
        forms[0]
        :
        ![12, 13, 14].includes(number) && [2, 3, 4].includes(number % 10)
        ?
        forms[1]
        :
        forms[2]
    );
}