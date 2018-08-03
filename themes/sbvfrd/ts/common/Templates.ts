/**
 * Provides an interface for string templates (not template strings!).
 */
export default class Templates {

    /**
     * Attempts to replace all keys from the given replacements by their values in the specified string template.
     *
     * @param {string} template
     * The string with placeholders to be replaced by the given replacements.
     *
     * @param {{[string]: any}} replacements
     * A map with keys matching the placeholder tokens and values for token replacement.
     *
     * @returns {string}
     * The resolved string.
     */
    public resolve(template: string, replacements: {[key: string] : any}): string {
        let result: string = template;

        for (let key in replacements) {
            let placeholder = '{' + key + '}';
            result = result.replace(placeholder, replacements[key]);
        }

        return result;
    }
}
