import Configuration from "./Configuration";
import SectionResult from "./SectionResult";
import Item from "./Item";


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
    public convert(configuration: Configuration, result: SearchResult): SectionResult {
        const sectionResult: SectionResult = { items: [], total: 0 };
        const data: SearchResultData = this.getResult(result);

        sectionResult.total = data.total;

        for (let index: number = 0; index < data.suggestions.length; ++index) {
            let item: Item = {
                label: data.suggestions[index].value,
                value: data.suggestions[index].id
            };

            item.href = configuration.getRecordLink(item);

            sectionResult.items[index] = item;
        }

        return sectionResult;
    }

    private getResult(result: SearchResult): SearchResultData {
        return result.data[SearchResultConverter.RESULT_INDEX];
    }
}


declare type SearchResultSuggestionItem = { id: string, value: string };
declare type SearchResultData = { total: number, suggestions: Array<SearchResultSuggestionItem> };

/**
 * Type definition for search results received for the per-section auto-suggest.
 */
export interface SearchResult {

    /**
     * The data received.
     */
    data: Array<SearchResultData>;

    /**
     * The response status.
     */
    status: string;
}