import { getInput } from '@actions/core';
import { executeStep } from '../actions.js';

type Options = {
    artifactId: number;
}

await executeStep('pull-artifact', {
    artifactId: Number(getInput('artifact_id')),
});

export type { Options as PullArtifactOptions };
