
// Declares the autocomplete() function itself and makes it a TypeScript type
declare type _VuFindAutoComplete = (options: string | VuFindAutoCompleteOptions) => this;

// Extends the type declared above to add all properties to the autocomplete function object itself
interface VuFindAutoComplete extends _VuFindAutoComplete {
    ajax: JQueryStatic.ajax;
}


// Type declaration for the callback function received from autocomplete.js in its handler
declare type _VuFindAutoCompleteCallback = (items: Array<string | VuFindAutoCompleteItem> | VuFindAutoCompleteItemCollection) => void;

interface VuFindAutoCompleteCallback extends _VuFindAutoCompleteCallback { }


/**
 * jQuery type extension to make the VuFind's autocomplete plugin accessible through TypScript.
 * @see {@link https://github.com/vufind-org/autocomplete.js}
 */
interface JQuery<TElement extends Node = HTMLElement> extends Iterable<TElement> {
    /**
     * @param {string | any} options
     * @returns {this}
     */
    autocomplete: VuFindAutoComplete;
}

interface VuFindAutoCompleteOptions {
    /**
     * A list of strings and items to use for every search. Matched without case sensitivity.
     */
    static?: Array<string | VuFindAutoCompleteItem>;

    /**
     * Optional matcher function to alter sorting behavior
     */
    staticSort?: (item1: VuFindAutoCompleteSortItem, item2: VuFindAutoCompleteSortItem) => number;

    /**
     * Optional handler to hook into search input handling e.g. by making AJAX requests before passing a result to the
     * callback.
     */
    handler?: (inputEl: JQuery, callback: VuFindAutoCompleteCallback) => void;

    /**
     * Milliseconds between last input and firing of AJAX
     * @default 200
     */
    ajaxDelay?: number;

    /**
     * Save results by term and reuse results if the same term is retyped
     * @default true
     */
    cache?: boolean;

    /**
     * Class added when the results are hidden and removed when revealed
     * @default 'hidden'
     */
    hidingClass?: string;

    /**
     * Give the search term some highlighting in the results
     * @default true
     */
    highlight?: boolean;

    /**
     * Pending AJAX phrase
     * @default 'Loading...'
     */
    loadingString?: string;

    /**
     * Most results shown
     * @default 20
     */
    maxResults?: number;

    /**
     * Minimum term length before firing
     * @default 3
     */
    minLength?: number;
}

interface VuFindAutoCompleteItem {

    /**
     * Optional display string for results list
     */
    label?: string;

    /**
     * The input value
     */
    value?: any;

    /**
     * Optional long text (rendered below the label by default)
     */
    description?: string;

    /**
     * Optional, go to a link instead of fill in input
     */
    href?: string;
}

interface VuFindAutoCompleteSortItem extends VuFindAutoCompleteItem {

    /**
     * The string on which the user's input matched.
     */
    match: string;
}

interface VuFindAutoCompleteItemCollection {
    /**
     * Results in a sectioned output to the autocomplete's dropdown.
     */
    groups: Array<Array<string | VuFindAutoCompleteItem> | VuFindAutoCompleteItemSection>;
}

interface VuFindAutoCompleteItemSection {
    /**
     * Displayed section header
     */
    label: string;

    /**
     * List of items to be rendered under the section header.
     */
    items: Array<string | VuFindAutoCompleteItem>;
}