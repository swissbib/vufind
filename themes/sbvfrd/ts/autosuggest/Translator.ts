/**
 * Interface for localization purposes that meets the VuFind translation API available on the global VuFind object
 * in JavaScript.
 */
export default interface Translator {

    /**
     * Resolves the given key to a localized string in the currently used language.
     *
     * @param {string} key
     * The localization key.
     *
     * @param {Array<any>} replacements
     * A list of values to replace indexed placeholders in the value that belongs to the key.
     *
     * @returns {string}
     * The resulting localized string with all placeholders resolved to the replacement values.
     */
    translate: (key: string, replacements?: any[]) => string;
}
