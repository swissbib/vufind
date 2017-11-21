import {AxiosPromise, AxiosResponse, default as Axios} from "axios";
import * as $ from "jquery";

declare global {
    let VuFind: any;
}

export class Hydra {

    /**
     * Should be more than:
     * "@context, @id, @type, id, firstName, lastName, label"
     */
    public static personHasSufficientData(data: object): boolean {
        const len = Object.keys(data).length;
        if (len < 8) {
            return false;
        } else {
            return true;
        }
    }

    private dataSwissBibUrl: string;
    private axiosConfig: object;

    constructor(dataSwissbibUrl: string) {
        this.dataSwissBibUrl = dataSwissbibUrl;
        this.axiosConfig = {
            baseURL: dataSwissbibUrl,
            // timeout: 100000,
            headers: {Accept: "application/ld+json"},
        };
    }

    public renderContributors(bibliographicResourceId: string,
                              htmlList: HTMLElement,
                              template: any): Promise<HTMLElement> {
        return this
            .getContributorUrls(bibliographicResourceId)
            .then((urls: string[]) => {
                return this.getContributorDetails(urls);
            })
            .then((contributors: Array<AxiosPromise<object>>) => {
                return Promise.all(contributors)
                    .then((contributor: object[]) => {
                        for (const p of contributor) {
                            $(template(p)).appendTo(htmlList);
                        }
                        return htmlList;
                    });
            });
    }

    /**
     * Fetches array with urls of all contributors
     *
     * @param {string} bibliographicResourceId
     * @returns {Promise<string[]>}
     */
    public getContributorUrls(bibliographicResourceId: string): Promise<string[]> {
        const url = this.dataSwissBibUrl + "bibliographicResource/" + bibliographicResourceId;
        return Axios.get<string[]>(url, this.axiosConfig)
            .then((response: AxiosResponse): string[] => {
                return response.data.contributor;
            });
    }

    /**
     * Fetches array with details of all contributors
     * @param {string[]} contributorUrls
     * @returns {AxiosPromise<any>[]}
     */
    public getContributorDetails(contributorUrls: string[]): Array<AxiosPromise<object>> {
        const promises: Array<AxiosPromise<object>> = [];
        for (const url of contributorUrls) {
            promises.push(Axios.get(url, this.axiosConfig)
                .then((response: AxiosResponse) => {
                    return response.data;
                }));
        }
        return promises;
    }

    /**
     * @deprecated Not used?
     * @param {string} contributorUrl
     * @returns {Promise<any>}
     */
    public getContributorDetail(contributorUrl: string): AxiosPromise<any> {
        return Axios.get(contributorUrl, this.axiosConfig);
    }

    public getContributorHtml(contributorPromise: Promise<object>, template: any): AxiosPromise<string> {
        return contributorPromise
            .then((person) => {
                const p: any = person;
                return template(p);
            });
    }

}
