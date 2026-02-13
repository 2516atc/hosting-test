import { context, executeStep } from '../actions.js';

type Options = {
    runId: number;
}

await executeStep('pre-staging', {
    runId: Number(context.github.run_id),
});

export type { Options as PreStagingOptions };
