import DataEntry from "./DataEntry";
import TemplateBase from "../common/Templates";
import ConfigurationItem from "./ConfigurationItem";

/**
 * Provides templates for carousel slide and data entry rendering.
 */
export default class Templates extends TemplateBase {

    /**
     * Constructor.
     *
     * @param {ConfigurationItem} configuration
     * The carousel configuration.
     */
    constructor(readonly configuration: ConfigurationItem) {
        super();
    }

    /**
     * Generates a string representing the DOM for a single slide for the carousel.
     *
     * @param {number} size
     * Determines the number of columns to generate into the slide.
     *
     * @param {number} remaining
     * In case remaining is greater than zero, then it is used as the number of columns to generate, while the sizing
     * of the columns is determined by the size parameter anyway.
     *
     * @return {string}
     */
    public slide(size: number, remaining: number = 0): string {
        const columnSize: number = 12 / size;
        const columnCount: number = remaining > 0 ? remaining : size;
        const columns:Array<string> = new Array(columnCount);

        for (let index: number = 0; index < columnCount; ++index) {
            columns[index] = `<div class="col-xs-${columnSize}">${this.emptyEntry()}</div>`;
        }

        const template: string =
            `<div class="item">` +
                //`<div class="container">` +
                `<div class="row">` +
                    `${columns.join('')}` +
                `</div>` +
                //`</div>` +
            `</div>`;

        return template;
    }

    /**
     * Generates a string representing the DOM for a single data entry that is rendered into a slide's column.
     *
     * @param {DataEntry} entry
     * The data entry to render.
     *
     * @return {string}
     */
    public entry(entry: DataEntry): string {
        const thumbnail: string = entry.thumbnail ? entry.thumbnail : this.configuration.thumbnail;
        const infoLink: string = entry.sufficientData ? `<a class="info-link" data-lightbox href="${this.info(entry)}"><span class="fa icon-info fa-lg" style="display: inline;"></span></a>`: ``;
        let imagePageLink: string;
        let labelPageLink: string;

        if (entry.id) {
            imagePageLink = `<a href="${this.page(entry)}"><img src="${thumbnail}"></a>`;
            labelPageLink = `<a href="${this.page(entry)}">${entry.displayName}</a>`;
        } else {
            imagePageLink = `<img src="${thumbnail}">`;
            labelPageLink = entry.displayName;
        }

        const template: string =
            `<div class="thumbnail-wrapper">${imagePageLink}</div>` +
            `<div class="label-wrapper">${labelPageLink}&nbsp;${infoLink}</div>`;

        return template;
    }

    /**
     * Renders an empty data entry view.
     *
     * @return {string}
     */
    public emptyEntry(): string {
        return this.entry({id: null, name: null, displayName: "&nbsp;", sufficientData: false, thumbnail: null});
    }

    /**
     * Resolves the AJAX URL to fetch data for the configured carousel.
     *
     * @param {number} page
     * The page to fetch data for.
     *
     * @param {number} size
     * The number of entries on the page.
     *
     * @return {string}
     */
    public ajax(page: number, size: number): string {
        return this.resolveConfigurationTemplate("ajax", {page: page, size: size});
    }

    /**
     * Resolves the page link template for the given data entry.
     *
     * @param {DataEntry} entry
     * The data entry to generate a page link for.
     *
     * @return {string}
     */
    public page(entry: DataEntry): string {
        return this.resolveConfigurationTemplate("page", {id: entry.id});
    }

    /**
     * Resolves the info link template for the given data entry.
     *
     * @param {DataEntry} entry
     * The data entry to generate an info link for.
     *
     * @return {string}
     */
    public info(entry: DataEntry): string {
        return this.resolveConfigurationTemplate("info", {id: entry.id});
    }

    /**
     * @private
     * @param {string} name
     * @param {Object} replacements
     * @return {string}
     */
    private resolveConfigurationTemplate(name: string, replacements: {[key: string] : any}): string {
        return this.resolve(Object(this.configuration.templates)[name], replacements);
    }
}