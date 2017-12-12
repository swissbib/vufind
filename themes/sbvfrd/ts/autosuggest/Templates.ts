/**
 * Internal class to keep template snippets in one place.
 */
export default class Templates {

    /**
     * Generates
     * @param {SectionHeader} args
     * @returns {string}
     */
    public sectionHeader(args: SectionHeader): string {
        return `<span class="section-label">${args.label}</span><a href="${args.target}">${args.targetLabel}</a>`;
    }

    /**
     *
     * @param {string} template
     * @param {{[key: string]: any}} replacements
     * @returns {string}
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

/**
 * Internal type declaration for the Templates.sectionHeader() method.
 */
interface SectionHeader {

    label: string;

    target: string;

    targetLabel: string;
}
