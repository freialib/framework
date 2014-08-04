<?php namespace hlin\archetype;

#
# This class is intentionally not an interface.
#

/**
 * Base exception class for namespace (and other related Panic classes).
 *
 * Each library namespace has an Panic class. Panics in the library are
 * treated as, literally, "exceptional cases." There are no "logic loops" with
 * exceptions (ie. InvalidPasswordException), if exceptions happen the
 * application is in an "undefined" and "unrecoverable [at the point in code]"
 * state. Invalid, but recoverable state, is generally passed though status
 * return statements (ie. true if went well, false if it failed) and only in
 * extreme circumstances exceptions.
 *
 * A Panic class is added to all library modules so that calling code has
 * some slight control over the origin of the exception when trying to recover.
 *
 * The class is called Panic, instead of Exception, because honestly the misuse
 * of the "Exception" pattern is so great it's now very hard to distinguish
 * from bad behavior. The term "Panic" is therefore a lot more suggestive of
 * how the class should be used.
 *
 * As an example, if you have an api, and said api then uses some other api for
 * the communication, it is relatively useful at the point of the call to be
 * able to distinguish if the "exceptional case" happened at the api level or
 * on another layer that the api might depend on. Beyond the point of the call
 * the "nature" of the exception becomes irrelevant in 99% of cases and it's
 * simply "did an exception happen?" and "at what level did the exception
 * happen?" typically for purpose of deciding how to report to the user, rather
 * then actually "recovering."
 *
 * There are cases where having a specialized exception can be "useful," such
 * as say "NoNetworkConnectionException" in an api, but unless a case can be
 * made that the exception is potentially useful in handling at the point of
 * call for specifically potential of recovery (which the previously given
 * exception example is very clearly), then the exception is NOT added. The
 * reason being that while "fancy exact reporting to the user" is nice it's
 * also time consuming and generates hundreds of exception classes who's only
 * point is to serve as a catch switch. This dilutes the distinction between
 * recoverable exceptions and unrecoverable exceptions so we just don't have
 * the reporting exception. If a library module has a distinct exception it's
 * something YOU SHOULD try to make use of, especially if the method has
 * a @throws clause for it.
 *
 * [!!] Calling code should not try to handle sub-exceptions but only the
 * exceptions of the code they're calling. So in code a catch should have the
 * exception with the namespace of the calling code; potentially another catch
 * for any other kind of exception if the calling code can actually handle it
 * or needs to mitigate all exceptions.
 *
 * [!!] A method will never use @throws with a non-library exception; not
 * even the default PHP \Exception as that doesn't make sense. It's the calling
 * code's responsibility to decide if they wish or not to handle undefined
 * exceptions coming from layers beneat the api they are calling.
 *
 * [!!] Even if the api throws an Panic the @throws statement will appear only
 * if it is considered easily recoverable or common. Though if it
 * is "too common" it may simply be integrated into the logic of the api as
 * a mandatory requirement for using it rather then an exception; which is to
 * say not using the exception mechanism at all.
 *
 * All library namespaces have their own Panic class and always simply throw
 * Panic in their code; ie. inherit the namespace of the module the code is
 * in.
 *
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class Panic extends \Exception {

	// empty

} # class
