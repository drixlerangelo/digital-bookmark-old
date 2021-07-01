/**
 * Tool for manipulating arrays
 *
 * @author drixlerangelo
 *
 * @since 1 July 2021
 */
export default {
    name: 'ArrayHelper',

    /**
     * Mirrors PHP array_column
     * Returns a new array derived from the column of the input array
     *
     * @param {String} column
     *
     * @param {Array} array
     *
     * @returns {Array}
     */
    arrayColumn(column, array) {
        let newArray = [];

        for (let row of array) {
            newArray.push(row[column]);
        }

        return newArray;
    }
}
