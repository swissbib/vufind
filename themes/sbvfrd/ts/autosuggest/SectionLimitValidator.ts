/**
 * Utility class to validate section limit values.
 */
export default class SectionLimitValidator {

    /**
     * A limit value is valid when it is a positive finite integer greater than 1.
     *
     * @param {number} limit
     * @returns {boolean}
     */
    public isValid(limit: number): boolean {
        return !isNaN(limit) && isFinite(limit) && Math.floor(limit) === limit && limit > 0;
    }

    /**
     * A limit value is valid when it is a positive finite integer greater than or equal to 0 (zero).
     *
     * @param {number} limit
     * @returns {boolean}
     */
    public isValidOrZero(limit: number): boolean {
        return this.isValid(limit) || limit === 0;
    }
}
