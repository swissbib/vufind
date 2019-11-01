import Section from "./Section";

export default interface Settings {
    /**
     * Configured sections to show in the auto-suggest result list.
     */
    readonly sections: Section[];

    /**
     * Indicates whether auto-suggest is enabled or not. If false, then no searches are performed at all.
     */
    readonly enabled: boolean;

    /**
     * A set of template strings to be filled with values on runtime.
     */
    readonly templates: {
        search: {

            /**
             * Search URL template for the auto-suggest result list
             */
            autosuggest: string,

            /**
             * Search URL template for a single record in the azto-suggest result list
             */
            record: string,
        },
    };
}
