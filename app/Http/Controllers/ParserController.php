<?php

namespace App\Http\Controllers;

use App\Services\Builder;
use App\Services\Parser;

/**
 * Class ParserController
 * @package App\Http\Controllers
 */
class ParserController extends Controller
{
    /**
     * @var Parser
     */
    public $parser;

    /**
     * @var Builder
     */
    public $builder;

    /**
     * ParserController constructor.
     * @param Parser $parser
     * @param Builder $builder
     */
    public function __construct(Parser $parser, Builder $builder)
    {
        $this->parser = $parser;
        $this->builder = $builder;
    }

    public function parse(): void
    {
        $this->parser->handle(12058625);
        $this->parser->handle(11927774);
    }

    public function build()
    {
        return view('index', ['data' => $this->builder->run(12058625)]);
        //return view('index', ['data' => $this->builder->run(11927774)]);
    }
}
