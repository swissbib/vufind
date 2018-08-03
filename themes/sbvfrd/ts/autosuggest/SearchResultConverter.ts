import Configuration from "./Configuration";
import Item from "./Item";
import SectionResult from "./SectionResult";
import Section from "./Section";

/**
 * Utility class that converts the results received from some search backend into VuFindAutoCompleteItem objects.
 */
export default class SearchResultConverter {

    /**
     * @private
     */
    private static RESULT_INDEX: number = 0;

    /**
     * Converts the given results as they have been received from the search backend into an array of structured items
     * as expected by the underlying auto-suggest interface.
     *
     * @param {Configuration} configuration
     * @param {SearchResult} result
     * @returns {SectionResult}
     */
    public convert(configuration: Configuration, section: Section, result: SearchResult): SectionResult {
        const sectionResult: SectionResult = { items: [], total: 0 };
        const data: SearchResultData = this.getResult(result);

        sectionResult.total = data.total;

        for (let index: number = 0; index < data.suggestions.length; ++index) {
            const item: Item = {
                label: data.suggestions[index].value,
                value: data.suggestions[index].id,
            };

            item.href = configuration.getRecordLink(item, section);

            sectionResult.items[index] = item;
        }

        return sectionResult;
    }

    private getResult(result: SearchResult): SearchResultData {
        return result.data[SearchResultConverter.RESULT_INDEX];
    }
}

declare interface SearchResultSuggestionItem { id: string; value: string; }
declare interface SearchResultData { total: number; suggestions: SearchResultSuggestionItem[]; }

/**
 * Type definition for search results received for the per-section auto-suggest.
 */
export interface SearchResult {

    /**
     * The data received.
     */
    data: SearchResultData[];

    /**
     * The response status.
     */
    status: string;
}
