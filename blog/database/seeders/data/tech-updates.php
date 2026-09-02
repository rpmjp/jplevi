<?php

/*
 * Copy for the demo posts.
 *
 * Kept out of the seeder because a seeder should read as a procedure and this
 * is an article. Bodies are the same HTML the editor produces, block markup
 * included, so what renders here is what renders after a real edit.
 */

$callout = fn (string $tone, string $title, string $body) => view('blocks.callout', compact('tone', 'title', 'body'))->render();
$quote = fn (string $text, ?string $attribution = null) => view('blocks.pull-quote', compact('text', 'attribution'))->render();

return [
    [
        'slug' => 'the-mcp-servers-we-actually-kept',
        'title' => 'The MCP servers we actually kept',
        'audience' => 'engineers',
        'excerpt' => 'We wired up eleven Model Context Protocol servers over a month of client work. Four survived contact with real tasks. Here is what separated them.',
        'social' => 'Eleven MCP servers in, four kept. The ones that survived had a boring quality in common.',
        'cover_alt' => 'Generated cover: a grid of blocks in the JP Levi palette.',
        'topics' => ['Tech updates', 'Engineering'],
        'body' => <<<HTML
<p>Model Context Protocol turned into the default way to hand an assistant a tool some time last year, and the number of servers you can install now comfortably exceeds the number worth installing. Over the past month we ran eleven of them against real client work rather than demos. Four are still in the config.</p>

<p>The split was not about which servers were well built. Most of them were. It was about something duller.</p>

{$callout('note', 'What we were testing', 'A server stayed if it made a task faster on the second week, not the first. Novelty makes everything feel useful once.')}

<h2>The ones that stayed</h2>

<p>Every survivor did one of two things: it read something we could not otherwise read, or it wrote somewhere we could not otherwise write. Filesystem access, a database the assistant could query directly, an issue tracker it could file into, and search. Four capabilities, four servers.</p>

<ul>
<li><strong>Filesystem.</strong> Obvious in hindsight. The whole value of an assistant in a codebase is that it can look.</li>
<li><strong>Postgres, read only.</strong> Being able to ask "how many rows actually have that null" and get a number ended more arguments than any amount of reasoning about the schema.</li>
<li><strong>The issue tracker.</strong> Not because filing tickets is hard, but because the ticket gets filed at the moment the problem is understood instead of an hour later.</li>
<li><strong>Search.</strong> Anything with a knowledge cutoff needs a way to check whether it is still right.</li>
</ul>

<h2>The ones that went</h2>

<p>The rest were wrappers around things we could already do in one command. A server that shells out to <code>git</code> is a server that stands between us and <code>git</code>. The assistant already had a terminal.</p>

{$quote('A tool that duplicates a command you already know is a tool you will stop reaching for by the second week.')}

<p>There is a second category worth naming, because it is the expensive one: servers that returned enormous responses. One of them handed back forty thousand tokens of directory listing for a question that needed a filename. Context is the budget, and a chatty tool spends it on your behalf without asking.</p>

<h2>What we look at now before installing one</h2>

<p>Three questions, in order.</p>

<ol>
<li>Does this reach something the assistant genuinely cannot reach on its own?</li>
<li>What is the largest response it can produce, and does it paginate?</li>
<li>What can it do that we would not want done without being asked? Anything that writes gets read-only credentials until it has earned otherwise.</li>
</ol>

{$callout('warning', 'On credentials', 'An MCP server runs with whatever access you hand it, and the assistant decides when to call it. Scope the token to what the task needs. A read-only database role costs nothing to create and removes a whole class of bad afternoon.')}

<h2>Where this is going</h2>

<p>The interesting part is not the protocol, it is that the tool surface is now something you curate rather than something a vendor ships you. That is a real shift, and it rewards the same discipline as any other dependency list: fewer, chosen deliberately, each one earning its place.</p>

<p>We will run the exercise again in a quarter. The list will probably be shorter.</p>
HTML,
        'comments' => [
            [
                'from' => 'Marcus Hale',
                'hours' => 5,
                'body' => "The response size point is underrated. We hit the same wall with a docs server that returned whole pages instead of sections. Nobody warns you about it because it works fine on a toy repo.",
                'replies' => [
                    [
                        'from' => 'Robert Jean Pierre',
                        'hours' => 3,
                        'body' => "That was exactly our failure mode. It is invisible until the repo gets big, and by then the habit is formed. Pagination should honestly be a requirement, not a feature.",
                    ],
                ],
            ],
            [
                'from' => 'Priya Raman',
                'hours' => 19,
                'body' => "Curious why you kept a separate search server rather than letting the model call an API directly. Was it just about not writing the glue?",
                'replies' => [
                    [
                        'from' => 'Robert Jean Pierre',
                        'hours' => 4,
                        'body' => "Mostly that, plus the server normalises results into something consistent. Writing the glue once is fine. Writing it once per project is where it stops being fine.",
                    ],
                ],
            ],
            [
                'from' => 'Dana Whitfield',
                'hours' => 30,
                'body' => "Read-only role by default is good advice for more than MCP. Half the incidents I have seen came from a credential that was broader than the job needed.",
            ],
        ],
    ],

    [
        'slug' => 'small-models-got-good-enough',
        'title' => 'Small models got good enough for the boring jobs',
        'audience' => 'both',
        'excerpt' => 'Classification, extraction, routing, tagging. The unglamorous 80% of what businesses actually ask AI to do no longer needs a frontier model, and the bill reflects it.',
        'social' => 'Most of what a business asks AI to do is boring. Boring is now cheap.',
        'cover_alt' => 'Generated cover: a grid of blocks in the JP Levi palette.',
        'topics' => ['Tech updates', 'Machine learning'],
        'body' => <<<HTML
<p>Almost every AI project we get called into contains one genuinely hard task and a dozen boring ones. Read this document and pull out four fields. Decide which of six queues this ticket belongs in. Tag this by topic. Is this message angry.</p>

<p>For two years the sensible default was to send all of it to the largest model available, because that was the thing that reliably worked. That default is now costing people money for no reason.</p>

<h2>What changed</h2>

<p>Small models, in the range that runs on one modest GPU or arrives as the cheapest tier of a hosted API, crossed the line where they do structured, narrow work as well as anything bigger. Not creative work. Not multi-step reasoning. The boring jobs.</p>

{$callout('result', 'On our own extraction task', 'Swapping a frontier model for a small one on invoice field extraction moved accuracy from 97.1% to 96.4% and cut the monthly bill by roughly ninety per cent. The seven-tenths of a point mattered less than the review queue we already had.')}

<p>That trade is the whole argument. A small model with a human review step on low-confidence outputs beats a large model with no review step, on both accuracy and cost, for anything where the output feeds a process rather than a person.</p>

<h2>Routing, not replacing</h2>

<p>The pattern that actually works is not "switch everything to the small model". It is a router: cheap model first, escalate on low confidence.</p>

<pre><code>if confidence >= 0.85:
    return small_model_result
return frontier_model(prompt)   # roughly 6% of traffic</code></pre>

<p>Six per cent escalation is a number we have seen hold across three separate client workloads. The exact threshold is worth tuning against your own labelled set, and the labelled set is worth building even if you never tune anything, because without it you are guessing.</p>

{$quote('The question was never which model is best. It is which model is enough, and how you find out when it is not.')}

<h2>Where a small model still fails</h2>

<ul>
<li><strong>Long context.</strong> Quality drops off well before the advertised window ends. Chunk it.</li>
<li><strong>Anything requiring several steps of reasoning.</strong> It will produce a confident, structured, wrong answer.</li>
<li><strong>Rare categories.</strong> If a label appears in under one per cent of your data, the small model has effectively not seen it.</li>
<li><strong>Writing that a customer reads.</strong> The difference is subtle and it is exactly the kind of subtle that people notice.</li>
</ul>

<h2>What this means if you are buying</h2>

<p>If a vendor is charging you per document and quoting frontier-model economics for field extraction, that is now a conversation worth having. The same is true internally. Look at what your highest-volume AI call actually does. If the answer is "picks one of five options", it is very likely overpaying.</p>

{$callout('note', 'A caveat worth stating', 'None of this applies to the hard task in the middle of your project. That one still wants the best model you can afford, and trying to save money there is how a project fails quietly.')}
HTML,
        'comments' => [
            [
                'from' => 'Tomas Reyes',
                'hours' => 8,
                'body' => "The 6% escalation figure lines up with what we see, which is reassuring. What are you using for the confidence signal? Logprobs, or a separate classifier?",
                'replies' => [
                    [
                        'from' => 'Robert Jean Pierre',
                        'hours' => 6,
                        'body' => "Logprobs where the provider exposes them, otherwise a small calibrated classifier over the output. The classifier is more work up front and behaves better at the threshold, so it is what we reach for when the volume justifies it.",
                    ],
                    [
                        'from' => 'Priya Raman',
                        'hours' => 14,
                        'body' => "Seconding the calibrated classifier. Raw logprobs were badly overconfident on our rare classes until we calibrated them.",
                    ],
                ],
            ],
            [
                'from' => 'Angela Boateng',
                'hours' => 26,
                'body' => "As a non-engineer reading this: the point about looking at your highest-volume call first is the one I can actually act on. Thank you for putting it in plain terms.",
            ],
        ],
    ],

    [
        'slug' => 'structured-outputs-retired-our-parser',
        'title' => 'Structured outputs retired most of our parsing code',
        'audience' => 'engineers',
        'excerpt' => 'We deleted about four hundred lines of defensive JSON handling this month. A note on what schema-constrained generation fixes, and the two failure modes it does not.',
        'social' => 'Four hundred lines of defensive JSON parsing, deleted. What schema-constrained output actually fixes.',
        'cover_alt' => 'Generated cover: a grid of blocks in the JP Levi palette.',
        'topics' => ['Tech updates', 'Engineering'],
        'body' => <<<HTML
<p>There is a file in most codebases that have talked to a language model for more than a year. Ours was called <code>SalvageJson.php</code>. It stripped markdown fences, found the outermost brace pair, fixed trailing commas, and had a comment at the top apologising for its own existence.</p>

<p>It is gone now, and the reason is worth writing down.</p>

<h2>The actual mechanism</h2>

<p>Schema-constrained generation is not the model being asked nicely for JSON. It is the decoder being prevented from emitting a token that would make the output invalid against your schema. If the grammar says the next character must be a quote, no other token is available to sample.</p>

{$callout('result', 'What that guarantees', 'The output parses, and it matches the shape you declared. Every time, not almost every time. That is a different category of promise from a prompt that says "respond only with JSON".')}

<p>The practical effect is that the whole defensive layer becomes dead code. Retry-on-parse-failure, the fence stripper, the brace matcher, the logging around all of it. Delete the lot.</p>

<h2>The two things it does not fix</h2>

<p>This is the part that catches people, because the guarantee is strong enough that it is tempting to assume it covers more than it does.</p>

<h3>The values can still be wrong</h3>

<p>A schema constrains shape, not truth. <code>{"invoice_total": 4200.00}</code> is perfectly valid when the invoice says 42.00. Validation still belongs downstream, and anything numeric that matters still wants a range check.</p>

{$quote('A guarantee about structure reads, at a glance, like a guarantee about content. It is not, and the gap is where the bugs live.')}

<h3>An over-tight schema degrades the answer</h3>

<p>This one surprised us. Constrain a field to a short enum and the model will pick from it — including when none of the options are right. The fix is unglamorous: add the escape hatch to the schema itself.</p>

<pre><code>"category": {
  "enum": ["billing", "technical", "account", "other"],
},
"category_confidence": { "type": "number" }</code></pre>

<p>Without <code>other</code>, every unclassifiable ticket becomes a confidently mislabelled ticket, and you find out about it from a customer.</p>

<h2>Migration notes</h2>

<ol>
<li>Declare the schema from your existing type, rather than by hand. A schema that drifts from the class it fills is worse than no schema.</li>
<li>Keep <code>additionalProperties: false</code>. Silent extra fields are how a rename goes unnoticed.</li>
<li>Make optional things genuinely optional. A required field the model cannot fill gets filled with something.</li>
<li>Keep the range checks. They were never about JSON.</li>
</ol>

{$callout('warning', 'Before you delete the parser', 'Check every provider you call. Support is not universal, and the failure mode on a provider that ignores the parameter is a plain string arriving where your code now assumes valid JSON. Ours was behind an interface, which is the only reason this was a one-day job.')}

<p>Four hundred lines down. The apology comment is the part I will miss least.</p>
HTML,
        'comments' => [
            [
                'from' => 'Marcus Hale',
                'hours' => 11,
                'body' => "The enum point is the real find here. We shipped a classifier with no escape hatch and spent a week convinced the model had gotten worse, when the truth is it never had anywhere to put the odd ones.",
                'replies' => [
                    [
                        'from' => 'Robert Jean Pierre',
                        'hours' => 5,
                        'body' => "Same story, almost exactly. Adding `other` plus a confidence number made the failure visible instead of silent, which is all we actually needed.",
                    ],
                ],
            ],
            [
                'from' => 'Dana Whitfield',
                'hours' => 22,
                'body' => "Generating the schema from the type rather than writing it twice is worth its own post. Every hand-written schema I have met has drifted.",
            ],
            [
                'from' => 'Tomas Reyes',
                'hours' => 40,
                'body' => "Did you measure any quality change after switching? I have seen claims in both directions and no numbers.",
                'replies' => [
                    [
                        'from' => 'Robert Jean Pierre',
                        'hours' => 7,
                        'body' => "On our extraction set it was flat within noise, so nothing worth reporting as a win. The gain was entirely in the code we stopped maintaining and the retries we stopped paying for.",
                    ],
                ],
            ],
        ],
    ],
];
