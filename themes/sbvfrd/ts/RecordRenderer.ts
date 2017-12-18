import {AxiosPromise} from "axios";
import * as $ from "jquery";
import {BibliographicDetails} from "./BibliographicDetails";
import {Contributor} from "./Contributor";
import {Detail} from "./Detail";
import {Hydra} from "./Hydra";
import {Subject} from "./Subject";

export class RecordRenderer {

    /**
     * Should be more than:
     * "@context, @id, @type, id, firstName, lastName, label"
     */
    public static personHasSufficientData(data: object): boolean {
        const len = Object.keys(data).length;
        if (len > 4) {
            return true;
        } else {
            return false;
        }
    }

    public static subjectHasSufficientData(p: any) {
        return true;
    }

    protected client: Hydra;

    constructor(dataUrl: string) {
        this.client = new Hydra(dataUrl);
    }

    public render(bibliographicResourceId: string, contributorsTemplate: any, contributorsHtml: HTMLElement,
                  subjectsTemplate: any, subjectsHtml: HTMLElement): Promise<HTMLElement[]> {
        return this.client.getBibliographicDetails(bibliographicResourceId)
            .then((bibliographicDetails: BibliographicDetails) => {
                const promises: Array<Promise<Detail[]>> = [];
                const contributorIds = bibliographicDetails.contributors;
                if (contributorIds && contributorIds.length > 0) {

                    promises.push(this.client.getContributorDetails(contributorIds));
                }
                const subjectIds = bibliographicDetails.subjects;
                if (subjectIds && subjectIds.length > 0) {
                    promises.push(this.client.getSubjectDetails(subjectIds));
                }
                return Promise.all(promises)
                    .then((details: Detail[][]) => {
                        const elements: HTMLElement[] = [];
                        for (const detail of details) {
                            // Unfortunately no overloading :-(
                            if (detail[0].type === "person") {
                                elements.push(
                                    this.renderDetails(detail, contributorsTemplate, contributorsHtml)
                                );
                            }
                            if (detail[0].type === "DEFAULT") {
                                elements.push(
                                    this.renderDetails(detail, subjectsTemplate, subjectsHtml)
                                );
                            }
                        }
                        return elements;
                    });
            });
    }

    public renderDetails(items: Detail[], template: any, htmlList: HTMLElement): HTMLElement {
        for (const p of items) {
            $(template(p)).appendTo(htmlList);
        }
        return htmlList;
    }

    public getContributorHtml(contributorPromise: Promise<Detail>, template: any): AxiosPromise<string> {
        return contributorPromise
            .then((person) => {
                const p: any = person;
                return template(p);
            });
    }

    // public renderContributorsOld(bibliographicResourceId: string,
    //                              htmlList: HTMLElement,
    //                              template: any): Promise<HTMLElement> {
    //     return this.client
    //         .getBibliographicDetails(bibliographicResourceId)
    //         .then((details: BibliographicDetails) => {
    //             return this.client.getContributorDetails(details.contributors);
    //         })
    //         .then((contributors: AxiosResponse<any[]>) => {
    //             return this.renderDetails(contributors, template, htmlList);
    //         });
    // }
}
