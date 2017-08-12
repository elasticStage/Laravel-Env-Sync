<?php
/**
 * Laravel-Env-Sync
 *
 * @author Julien Tant - Craftyx <julien@craftyx.fr>
 */

namespace Jtant\LaravelEnvSync\Tests\Writer\File;


use Jtant\LaravelEnvSync\Writer\File\EnvFileWriter;
use Jtant\LaravelEnvSync\Writer\WriterInterface;
use org\bovigo\vfs\vfsStream;

class EnvFileWriterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \org\bovigo\vfs\vfsStreamDirectory
     */
    private $fs;
    /**
     * @var WriterInterface
     */
    private $writer;

    protected function setUp()
    {
        $this->writer = new EnvFileWriter();
        $this->fs = vfsStream::setup("write_env");
    }

    protected function tearDown()
    {
        parent::tearDown(); // TODO: Change the autogenerated stub
    }


    /** @test */
    public function it_should_append_content_to_file()
    {
        app()->instance(\Illuminate\Contracts\Debug\ExceptionHandler::class, new class extends \Illuminate\Foundation\Exceptions\Handler {
            public function __construct() {}
            public function report(\Exception $e) {}
            public function render($request, \Exception $e)
            {
                echo $e->getMessage();
                throw $e;
            }
        });


        // Arrange
        $filePath = $this->fs->url() . '/.env';

        $lines = [
            'test=foo',
            'foo=baz',
        ];
        file_put_contents($filePath, implode(PHP_EOL, $lines));

        // Act
        $this->writer->append($filePath, 'phpunit', 'rocks hard');

        // Assert
        $lines = file($filePath);

        $this->assertEquals([
            "test=foo\n",
            "foo=baz\n",
            "phpunit=\"rocks hard\""
        ], $lines);
    }
}
