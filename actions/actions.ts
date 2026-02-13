import { getInput } from '@actions/core';
import { request } from 'node:https';
import type { PreStagingOptions } from './pre-staging/main.js';
import type { PullArtifactOptions } from './pull-artifact/main.js';

type GithubContext = {
    repository: string;
    run_id: string;
};

type Steps = {
    'pre-staging': PreStagingOptions;
    'pull-artifact': PullArtifactOptions;
}

const context = {
    get github(): GithubContext
    {
        return JSON.parse(getInput('github_context'));
    }
};

const executeStep = async <T extends keyof Steps>(step: T, options: Steps[T]): Promise<void> => {
    return new Promise((resolve) => {
        const stepRequest = request(
            `https://hosting.2516droitwichsquadron.co.uk/deploy/${context.github.repository}/step`,
            { method: 'PUT' },
            (response) => {
                response.resume().on('end', () => resolve());
            }
        );

        stepRequest.write({ step, options });
        stepRequest.end();
    });
}

export { context, executeStep };
