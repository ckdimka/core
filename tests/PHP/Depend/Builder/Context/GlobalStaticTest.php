<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2010, Manuel Pichler <mapi@pdepend.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  QualityAssurance
 * @package   Builder_Context
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2010 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../../AbstractTest.php';

/**
 * Test case for the {@link PHP_Depend_Builder_Context_GlobalStatic} class.
 *
 * @category  QualityAssurance
 * @package   Builder_Context
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2010 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 *
 * @covers PHP_Depend_Builder_Context_GlobalStatic
 */
class PHP_Depend_Builder_Context_GlobalStaticTest extends PHP_Depend_AbstractTest
{
    /**
     * testRegisterClassCallsRestoreClassOnBuilder
     *
     * @return void
     * @group pdepend
     * @group pdepend::builder
     * @group pdepend::builder::context
     * @group unittest
     */
    public function testRegisterClassCallsRestoreClassOnBuilder()
    {
        $builder = $this->getMock('PHP_Depend_BuilderI');
        $builder->expects($this->once())
            ->method('restoreClass')
            ->with(self::isInstanceOf(PHP_Depend_Code_Class::TYPE));

        $context = new PHP_Depend_Builder_Context_GlobalStatic($builder);
        $context->registerClass(new PHP_Depend_Code_Class(__CLASS__));
    }

    /**
     * testRegisterInterfaceCallsRestoreInterfaceOnBuilder
     *
     * @return void
     * @group pdepend
     * @group pdepend::builder
     * @group pdepend::builder::context
     * @group unittest
     */
    public function testRegisterInterfaceCallsRestoreInterfaceOnBuilder()
    {
        $builder = $this->getMock('PHP_Depend_BuilderI');
        $builder->expects($this->once())
            ->method('restoreInterface')
            ->with(self::isInstanceOf(PHP_Depend_Code_Interface::TYPE));

        $context = new PHP_Depend_Builder_Context_GlobalStatic($builder);
        $context->registerInterface(new PHP_Depend_Code_Interface(__CLASS__));
    }

    /**
     * testGetClassDelegatesCallToWrappedBuilder
     *
     * @return void
     * @group pdepend
     * @group pdepend::builder
     * @group pdepend::builder::context
     * @group unittest
     */
    public function testGetClassDelegatesCallToWrappedBuilder()
    {
        $builder = $this->getMock('PHP_Depend_BuilderI');
        $builder->expects($this->once())
            ->method('getClass')
            ->with(self::equalTo(__CLASS__));

        $context = new PHP_Depend_Builder_Context_GlobalStatic($builder);
        $context->getClass(__CLASS__);
    }

    /**
     * testGetClassOrInterfaceDelegatesCallToWrappedBuilder
     *
     * @return void
     * @group pdepend
     * @group pdepend::builder
     * @group pdepend::builder::context
     * @group unittest
     */
    public function testGetClassOrInterfaceDelegatesCallToWrappedBuilder()
    {
        $builder = $this->getMock('PHP_Depend_BuilderI');
        $builder->expects($this->once())
            ->method('getClassOrInterface')
            ->with(self::equalTo(__CLASS__));

        $context = new PHP_Depend_Builder_Context_GlobalStatic($builder);
        $context->getClassOrInterface(__CLASS__);
    }
}