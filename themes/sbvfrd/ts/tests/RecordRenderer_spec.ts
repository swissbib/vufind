import * as fs from "fs";
import * as $ from "jquery";
import RecordRenderer from "../RecordRenderer";
import Subject from "../Subject";
import Detail from "../Detail";

// const mock = jest.genMockFromModule("Hydra");

const Mock = jest.fn(() => ({
    getBibliographicDetails: jest.fn((ids) => {
        return readFixture("bibliographicResource");
    }),
    getPersonDetails: jest.fn((ids) => {
        return readFixture("contributors");
    }),
    getSubjectDetails: jest.fn((ids) => {
        return readFixture("subjects");
    }),
}));

const cut: RecordRenderer = new RecordRenderer("");
cut.client = new Mock();

function readFixture(fileName: string) {
    return new Promise((resolve, reject) =>
        fs.readFile(`themes/sbvfrd/ts/tests/fixtures/${ fileName }.json`, "utf8",
            (err: any, data: any) => {
                if (err) {
                    reject(err);
                }
                resolve(
                    JSON.parse(data));
            }));
}

it("should create Html", () => {
    expect.assertions(1);

    const templateFn = (p: any): string => {
        return `${p.lastName}, ${p.firstName}`;
    };

    const actual = cut.getContributorHtml(
        readFixture("person-5f679432-5f41-3bd8-a19f-8a20c4431aea"), templateFn);
    return expect(actual).resolves.toContain("Bamber, David");
});

it("Html should contain list element with contributors", () => {
    const body = document.getElementsByTagName("body")[0];
    const list = document.createElement("ul");
    body.appendChild(list);

    expect.assertions(2);

    const contributorsTemplate = (p: Detail) => {
        return `<li>${p.name}</li>`;
    };
    const contributorsList = $(list)[0];

    return cut.renderContributors("023426233", contributorsTemplate, contributorsList)
        .then((html: HTMLElement[]) => {
            const actual: JQuery<HTMLElement> = $(html[0]);
            expect(actual.children("li").length).toBe(10);
            expect(actual.find("li").get(0).innerHTML).toEqual("Bamber, David");
        })
        ;
});

it("Html should contain value from response", () => {
    const li = document.createElement("li");
    li.setAttribute("subjectid", "4156468-6");
    const subjectList: JQuery<HTMLElement> = $(li);

    expect.assertions(1);

    const template = (s: Subject) => {
        return `${s.name}`;
    };

    return cut.renderSubjects(subjectList, template)
        .then(() => {
            expect($(subjectList.get(0)).html()).toEqual("Gemäldegalerie");
        });
});
