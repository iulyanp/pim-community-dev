<?php

namespace spec\Pim\Component\Connector\Writer\File\Csv;

use Akeneo\Component\Batch\Job\JobParameters;
use Akeneo\Component\Batch\Model\StepExecution;
use Akeneo\Component\Buffer\BufferInterface;
use PhpSpec\ObjectBehavior;
use Pim\Component\Connector\ArrayConverter\ArrayConverterInterface;
use Pim\Component\Connector\Writer\File\FilePathResolverInterface;
use Pim\Component\Connector\Writer\File\FlatItemBuffer;
use Pim\Component\Connector\Writer\File\FlatItemBufferFlusher;
use Prophecy\Argument;

class WriterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Pim\Component\Connector\Writer\File\Csv\Writer');
    }

    function let(
        ArrayConverterInterface $arrayConverter,
        FilePathResolverInterface $filePathResolver,
        FlatItemBuffer $flatRowBuffer,
        FlatItemBufferFlusher $flusher
    ) {
        $this->beConstructedWith($arrayConverter, $filePathResolver, $flatRowBuffer, $flusher);

        $filePathResolver->resolve(Argument::any(), Argument::type('array'))
            ->willReturn('/tmp/export/export.csv');
    }

    function it_is_a_configurable_step()
    {
        $this->shouldHaveType('Akeneo\Component\Batch\Item\AbstractConfigurableStepElement');
    }

    function it_is_a_writer()
    {
        $this->shouldImplement('Akeneo\Component\Batch\Item\ItemWriterInterface');
    }

    function it_prepares_the_export(
        $arrayConverter,
        $flatRowBuffer,
        StepExecution $stepExecution,
        JobParameters $jobParameters
    ) {
        $this->setStepExecution($stepExecution);
        $stepExecution->getJobParameters()->willReturn($jobParameters);
        $jobParameters->get('filePath')->willReturn('my/file/path/foo');
        $jobParameters->has('mainContext')->willReturn(false);
        $jobParameters->get('withHeader')->willReturn(true);

        $groups = [
            [
                'code'   => 'promotion',
                'type'   => 'RELATED',
                'labels' => ['en_US' => 'Promotion', 'de_DE' => 'Förderung']
            ],
            [
                'code'   => 'related',
                'type'   => 'RELATED',
                'labels' => ['en_US' => 'Related', 'de_DE' => 'Verbunden']
            ]
        ];

        $arrayConverter->convert([
            'code'   => 'promotion',
            'type'   => 'RELATED',
            'labels' => ['en_US' => 'Promotion', 'de_DE' => 'Förderung']
        ])->willReturn([
            'code'        => 'promotion',
            'type'        => 'RELATED',
            'label-en_US' => 'Promotion',
            'label-de_DE' => 'Förderung'
        ]);

        $arrayConverter->convert([
            'code'   => 'related',
            'type'   => 'RELATED',
            'labels' => ['en_US' => 'Related', 'de_DE' => 'Verbunden']
        ])->willReturn([
            'code'        => 'related',
            'type'        => 'RELATED',
            'label-en_US' => 'Related',
            'label-de_DE' => 'Verbunden'
        ]);

        $flatRowBuffer->write(
            [
                [
                    'code'        => 'promotion',
                    'type'        => 'RELATED',
                    'label-en_US' => 'Promotion',
                    'label-de_DE' => 'Förderung'
                ],
                [
                    'code'        => 'related',
                    'type'        => 'RELATED',
                    'label-en_US' => 'Related',
                    'label-de_DE' => 'Verbunden'
                ]
            ],
            true
        )->shouldBeCalled();

        $this->write($groups);
    }

    function it_writes_the_csv_file(
        $flusher,
        $flatRowBuffer,
        StepExecution $stepExecution,
        JobParameters $jobParameters
    ) {
        $this->setStepExecution($stepExecution);

        $flusher->setStepExecution($stepExecution)->shouldBeCalled();

        $stepExecution->getJobParameters()->willReturn($jobParameters);
        $jobParameters->has('linesPerFile')->willReturn(false);
        $jobParameters->get('delimiter')->willReturn(';');
        $jobParameters->get('enclosure')->willReturn('"');
        $jobParameters->get('filePath')->willReturn('my/file/path/foo');
        $jobParameters->has('mainContext')->willReturn(false);

        $flusher->flush(
            $flatRowBuffer,
            Argument::type('array'),
            Argument::type('string'),
            -1,
            Argument::type('array')
        )->willReturn(['my/file/path/foo1', 'my/file/path/foo2']);

        $this->flush();
    }
}