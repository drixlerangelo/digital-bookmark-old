/**
 * A library that serves as a validation mechanism for user inputs
 *
 * @author drixlerangelo
 *
 * @since 21 December 2020
 */
export default {
    /**
     * Name of the library
     *
     * @var {String}
     */
    name  : 'Validator',

    /**
     * User input
     *
     * @var {String}
     */
    input : '',

    /**
     * Name of the field where the places inputs
     *
     * @var {String}
     */
    field : '',

    /**
     * Prepares the value of the field and its input
     *
     * @param {String} field
     *
     * @param {String} input
     */
    setParams(field, input = '') {
        this.input = input || '';
        this.field = field;
    },

    /**
     * Tests if the string length or the number is within the accepted ranges
     *
     * @param {Number} min
     *
     * @param {Number} max
     */
    validateLength(min, max, type = 'string') {
        let value = (type === 'string') ? this.input.length : this.input;
        let {minValue, maxValue} = (type === 'date') ? [min.toDateString(), max.toDateString()] : [min, max];

        const errorMessages = {
            string : {
                minimum : `A minimum of ${minValue} character/s is needed.`,
                maximum : `A maximum of ${maxValue} character/s is needed.`
            },
            number : {
                minimum : `The number must be at least ${minValue}.`,
                maximum : `The number must be at most ${maxValue}.`
            },
            date   : {
                minimum : `The date must be at least ${minValue}.`,
                maximum : `The date must be at most ${maxValue}.`
            }
        };

        window.ErrorManager.testCondition(value < min, this.field, errorMessages[type].minimum);
        window.ErrorManager.testCondition(value > max, this.field, errorMessages[type].maximum);
    },

    /**
     * Tests if the string formatting is correct
     *
     * @param {String} pattern
     */
    validateFormat(pattern) {
        const regex = RegExp(pattern);

        window.ErrorManager.testCondition(
            regex.test(this.input) === false,
            this.field,
            `The ${this.field} is in an invalid format. `
        );
    },

    /**
     * Tests if the file size is accepted
     *
     * @param {Number} min
     *
     * @param {Number} max
     */
    validateFileSize(min, max) {
        const BYTES_IN_MB = 1048576;
        const BYTES_IN_KB = 1024;
        const BYTES_IN_B = 1;
        const units = {
            MB : BYTES_IN_MB,
            KB : BYTES_IN_KB,
            B : BYTES_IN_B
        };

        let [minSize, minAbbrev] = min.split(' ');
        minSize = parseInt(minSize) * units[minAbbrev.trim().toUpperCase()];
        let [maxSize, maxAbbrev] = max.split(' ');
        maxSize = parseInt(maxSize) * units[maxAbbrev.trim().toUpperCase()];

        window.ErrorManager.testCondition(this.input.size < minSize, this.field, `The file must be at least ${minSize}${minAbbrev}.`);
        window.ErrorManager.testCondition(this.input.size > maxSize, this.field, `The file must be at most ${maxSize}${maxAbbrev}.`);
    },

    /**
     * Tests if the file's mime type is allowed
     *
     * @param {Object} allowedTypes
     */
    validateMimeType(allowedTypes = []) {
        const fileExtensions = {
            'image/gif'  : 'GIF',
            'image/png'  : 'PNG',
            'image/jpeg' : 'JPEG, JPG'
        };
        let allowedExtensions = [];

        for (let key in allowedTypes) {
            let mime = allowedTypes[key];
            allowedExtensions.push(fileExtensions[mime]);
        };

        window.ErrorManager.testCondition(
            allowedTypes.indexOf(this.input.type) === -1,
            this.field,
            `${allowedExtensions.join(', ')} are the only files allowed.`
        );
    }
};
