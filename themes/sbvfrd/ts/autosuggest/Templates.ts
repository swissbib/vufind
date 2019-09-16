
import TemplateBase from "../common/Templates";

/**
 * Internal class to keep template snippets in one place.
 */
export default class Templates extends TemplateBase {

    /**
     * Generates
     * @param {SectionHeader} args
     * @returns {string}
     */
    public sectionHeader(args: SectionHeader): string {
        return `<span class="section-label">${args.label}</span>`;
    }
}

/**
 * Internal type declaration for the Templates.sectionHeader() method.
 */
interface SectionHeader {

    label: string;
}
