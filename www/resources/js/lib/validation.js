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
     * Tests if the string length is within the accepted ranges
     * 
     * @param {Number} min 
     * 
     * @param {Number} max 
     */
    validateLength(min, max) {
        window.ErrorManager.testCondition(
            this.input.length < min, 
            this.field, 
            `A minimum of ${min} characters is needed.`
        );

        window.ErrorManager.testCondition(
            this.input.length > max, 
            this.field, 
            `A maximum of ${max} characters is needed.`
        );
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
    }
};
