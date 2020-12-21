/**
 * Manages the error in the application
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
    name    : 'ErrorManager',

    /**
     * Shows if an error is found
     * 
     * @var {Boolean}
     */
    status  : false,

    /**
     * Storage for the errors found
     * 
     * @var {Array}
     */
    list    : {},

    /**
     * Counts the errors found
     * 
     * @var {Number}
     */
    count   : 0,
    
    /**
     * Performs a check and records the error if found
     * 
     * @param {Boolean} condition 
     * 
     * @param {String} field 
     * 
     * @param {String} message 
     */
    testCondition(condition, field, message) {
        this.setFieldError(field);
        
        if (condition === true) {
            this.addError(field, message);
        } else {
            this.removeError(field, message);
        }
    },

    /**
     * Initializes the storage of errors by a field
     * 
     * @param {String} field 
     */
    setFieldError(field) {
        if (this.list[field] === undefined) {
            this.list[field] = [];
        }
    },

    /**
     * If an error is found, it is recorded
     * 
     * @param {String} field 
     * 
     * @param {String} message 
     */
    addError(field, message) {
        let index = this.list[field].indexOf(message);

        if (index === -1) {
            this.list[field].push(message);
            this.count ++;
        }

        if (this.status === false) {
            this.status = true;
        }
    },

    /**
     * If there's no error, it removes the message from the listed errors
     * 
     * @param {String} field 
     * 
     * @param {String} message 
     */
    removeError(field, message) {
        let index = this.list[field].indexOf(message);

        if (index !== -1) {
            this.list[field].splice(index, 1);
            this.count -= 1;

            if (this.count === 0) {
                this.status = false;
            }
        }
    },

    /**
     * Retrieves the status if an error is found
     * 
     * @returns {Boolean}
     */
    getStatus() {
        return this.status;
    },

    /**
     * Fetch the result from the error-checking
     * 
     * @param {String} field 
     * 
     * @returns {Object}
     */
    getResult(field) {
        return {
            passed  : this.list[field].length === 0,
            message : this.list[field][0] || ''
        };
    }
};
