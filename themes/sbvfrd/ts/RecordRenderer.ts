import {AxiosPromise} from "axios";
import * as $ from "jquery";
import {BibliographicDetails} from "./BibliographicDetails";
import {Detail} from "./Detail";
import {Hydra} from "./Hydra";

export class RecordRenderer {

    protected client: Hydra;

    constructor(dataUrl: string) {
        this.client = new Hydra(dataUrl);
    }

    public render(bibliographicResourceId: string, contributorsTemplate: any, contributorsHtml: HTMLElement): Promise<HTMLElement[]> {
        return this.client.getBibliographicDetails(bibliographicResourceId)
            .then((bibliographicDetails: BibliographicDetails) => {
                const promises: Array<Promise<Detail[]>> = [];
                const contributorIds = bibliographicDetails.contributors;
                if (contributorIds && contributorIds.length > 0) {
                    promises.push(this.client.getContributorDetails(contributorIds));
                }
                return Promise.all(promises)
                    .then((details: Detail[][]) => {
                        const elements: HTMLElement[] = [];
                        for (const detail of details) {
                            elements.push(
                                this.renderDetails(detail, contributorsTemplate, contributorsHtml),
                            );
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

    public renderSubjects(subjects: JQuery<HTMLElement>, template: any) {
        subjects.each((i, el) => {
            const id: string = $(el).attr("subjectid");
            const subjectDetails = this.client.getSubjectDetails("http://d-nb.info/gnd/" + id);
            subjectDetails
                .then((s) => {
                    if (s && s.length === 1) {
                        $(el).append(template(s[0]));
                    }
                });
        });
    }
}
